#!/usr/bin/env python3
"""Root-only, drift-checked installation of reviewed Nginx token-safe logging."""
import hashlib
import os
from pathlib import Path
import pwd
import grp
import subprocess
import tempfile
import time

SOURCE = Path(__file__).resolve().parent
BASELINES = {
    Path('/etc/nginx/nginx.conf'): '48c6a4ec1e1fd28ccf968490f07e34a1d7f755793b2108a3ed8670b1ee2a0aa2',
    Path('/etc/nginx/sites-available/jbr-public'): '6e333ee372dec22994e8096deb593ffe6305da0ca3e26ecb4fed860e073643ac',
    Path('/etc/nginx/sites-available/jbr-review'): 'a7046e0b48bb02533a47a1265c5456115d5d5c652293eff2f005a2dd80641e51',
}
FILES = {
    '/etc/nginx/jbr-token-audit.conf': 'nginx-token-audit.conf',
    '/etc/nginx/sites-available/jbr-public': 'nginx-jbr-public.conf',
    '/etc/nginx/sites-available/jbr-review': 'nginx-jbr-review.conf',
    '/etc/rsyslog.d/30-jbr-nginx-safe.conf': 'rsyslog-nginx-token-redaction.conf',
    '/etc/tmpfiles.d/jbr-nginx-log.conf': 'jbr-nginx-log.tmpfiles',
    '/etc/logrotate.d/jbr-nginx-sanitized': 'jbr-nginx-log.logrotate',
}


def run(*command: str) -> None:
    subprocess.run(command, check=True)


def write(path: Path, content: bytes) -> None:
    with tempfile.NamedTemporaryFile(dir=path.parent, delete=False) as file:
        temporary = Path(file.name)
        file.write(content)
        file.flush()
        os.fsync(file.fileno())
    try:
        temporary.chmod(0o644)
        os.replace(temporary, path)
    finally:
        temporary.unlink(missing_ok=True)


def main() -> None:
    if os.geteuid() != 0:
        raise RuntimeError('Run as root using the reviewed, root-owned staging directory.')
    for path, expected in BASELINES.items():
        if path.is_symlink() or hashlib.sha256(path.read_bytes()).hexdigest() != expected:
            raise RuntimeError('Live configuration drifted; review before installation.')
    for destination in FILES:
        if Path(destination) not in BASELINES and Path(destination).exists():
            raise RuntimeError('A new logging destination already exists; review before installation.')
    nginx = Path('/etc/nginx/nginx.conf')
    original_nginx = nginx.read_bytes()
    old_directive = b'error_log /var/log/nginx/error.log;'
    if original_nginx.count(old_directive) != 1:
        raise RuntimeError('Global error-log directive does not match the reviewed configuration.')
    contents = {Path(destination): (SOURCE / filename).read_bytes() for destination, filename in FILES.items()}
    if original_nginx.count(b'http {\n') != 1 or original_nginx.count(b'access_log /var/log/nginx/access.log;') != 1:
        raise RuntimeError('Global request-audit layout does not match the reviewed configuration.')
    contents[nginx] = original_nginx.replace(old_directive, b'error_log syslog:server=unix:/var/log/jbr-nginx-socket/error.sock,tag=jbr_global error;\n# Prevent Nginx from creating an additional raw default/stderr error log.\nerror_log /dev/null emerg;').replace(b'http {\n', b'http {\n    include /etc/nginx/jbr-token-audit.conf;\n').replace(b'access_log /var/log/nginx/access.log;', b'access_log /var/log/nginx/access.log jbr_token_safe;')
    originals = {path: path.read_bytes() if path.exists() else None for path in contents}
    backup = Path('/var/backups/jbr-token-logging-' + time.strftime('%Y%m%dT%H%M%SZ', time.gmtime()))
    backup.mkdir(mode=0o700)
    for path, original in originals.items():
        if original is not None:
            copy = backup / str(path).lstrip('/').replace('/', '__')
            copy.write_bytes(original)
            copy.chmod(0o600)
    try:
        for path, content in contents.items():
            write(path, content)
        run('systemd-tmpfiles', '--create', '/etc/tmpfiles.d/jbr-nginx-log.conf')
        run('rsyslogd', '-N1')
        run('nginx', '-t')
        run('systemctl', 'restart', 'rsyslog')
        run('systemctl', 'is-active', '--quiet', 'rsyslog')
        socket_path = Path('/var/log/jbr-nginx-socket/error.sock')
        for attempt in range(50):
            if socket_path.exists():
                break
            time.sleep(0.1)
        else:
            raise RuntimeError('Redacting error-log socket did not start.')
        os.chown(socket_path, pwd.getpwnam('syslog').pw_uid, grp.getgrnam('www-data').gr_gid)
        socket_path.chmod(0o660)
        run('systemctl', 'reload', 'nginx')
        run('systemctl', 'is-active', '--quiet', 'nginx')
    except BaseException:
        for path, original in originals.items():
            if original is None:
                path.unlink(missing_ok=True)
            else:
                write(path, original)
        run('rsyslogd', '-N1')
        run('nginx', '-t')
        run('systemctl', 'restart', 'rsyslog')
        run('systemctl', 'reload', 'nginx')
        raise
    print('Token-safe logging installed; audit and error logging remain enabled. Backup: ' + str(backup))


if __name__ == '__main__':
    main()
