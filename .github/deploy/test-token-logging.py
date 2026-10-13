#!/usr/bin/env python3
"""Exercise real isolated Nginx/rsyslog processes with synthetic tokens only."""
import http.client
import json
import os
from pathlib import Path
import signal
import socket
import subprocess
import tempfile
import time

SOURCE = Path(__file__).resolve().parent
TOKEN = 'a1' * 32
OTHER_TOKEN = 'B2' * 32


def unused_port() -> int:
    with socket.socket() as listener:
        listener.bind(('127.0.0.1', 0))
        return listener.getsockname()[1]


def wait_for(predicate) -> None:
    for attempt in range(100):
        if predicate():
            return
        time.sleep(0.05)
    raise AssertionError('Isolated logging process did not become ready.')


def main() -> None:
    with tempfile.TemporaryDirectory(prefix='jbr-logging-test-', dir='/var/log' if os.geteuid() == 0 else None) as directory, tempfile.TemporaryDirectory(prefix='jbr-test-', dir='/etc/rsyslog.d' if os.geteuid() == 0 else None) as config_directory:
        root = Path(directory)
        socket_path = root / 'error.sock'
        error_file = root / 'errors.log'
        access_file = root / 'access.log'
        error_config = (SOURCE / 'rsyslog-nginx-token-redaction.conf').read_text()
        error_config = error_config.replace('/var/log/jbr-nginx-socket/error.sock', str(socket_path))
        error_config = error_config.replace('/var/log/jbr-nginx/errors.log', str(error_file))
        error_config = error_config.replace('fileOwner="syslog" fileGroup="adm"', '')
        rsyslog_config = Path(config_directory) / 'rsyslog.conf'
        rsyslog_config.write_text('module(load="imuxsock" SysSock.Use="off")\n' + error_config)
        validation = subprocess.run(['rsyslogd', '-N1', '-f', str(rsyslog_config)], capture_output=True)
        if validation.returncode:
            raise RuntimeError(validation.stderr.decode())
        port = unused_port()
        failed_upstream = unused_port()
        default_port = unused_port()
        global_access_file = root / 'global-access.log'
        user = 'user root;\n' if os.geteuid() == 0 else ''
        config = (
            user + f'pid {root}/nginx.pid;\nerror_log syslog:server=unix:{socket_path},tag=jbr_test error;\nerror_log /dev/null emerg;\n'
            'events {}\nhttp {\n' + (SOURCE / 'nginx-token-audit.conf').read_text() + '\n'
            f'access_log {global_access_file} jbr_token_safe;\n'
            f'client_body_temp_path {root}/body; proxy_temp_path {root}/proxy;\n'
            f'server {{ listen 127.0.0.1:{default_port} default_server; server_name _; return 404; }}\n'
            f'server {{ listen 127.0.0.1:{port}; server_name localhost;\n'
            f'access_log {access_file} jbr_token_safe;\n'
            'add_header Referrer-Policy $jbr_referrer_policy always;\n'
            'location /ok { return 200 "ok"; }\n'
            'location /redirect { return 302 https://preview.jauharat.com$request_uri; }\n'
            f'location /admin/password-reset/reset {{ proxy_pass http://127.0.0.1:{failed_upstream}; }}\n'
            f'location /broken {{ proxy_pass http://127.0.0.1:{failed_upstream}; }}\n'
            'location /denied { deny all; }\n'
            f'location /private-review {{ auth_basic "test"; auth_basic_user_file {root}/missing.htpasswd; }}\n'
            f'location / {{ root {root}/empty; }}\n}}\n}}\n'
        )
        nginx_config = root / 'nginx.conf'
        nginx_config.write_text(config)
        stderr_path = root / 'process-errors.log'
        with stderr_path.open('w') as stderr:
            rsyslog = subprocess.Popen(['rsyslogd', '-n', '-f', str(rsyslog_config), '-i', str(root / 'rsyslog.pid')], stdout=stderr, stderr=stderr)
            nginx = None
            try:
                wait_for(socket_path.exists)
                validation = subprocess.run(['nginx', '-e', str(root / 'raw-fallback.log'), '-t', '-p', str(root), '-c', str(nginx_config)], capture_output=True)
                if validation.returncode:
                    raise RuntimeError(validation.stderr.decode())
                nginx = subprocess.Popen(['nginx', '-e', str(root / 'raw-fallback.log'), '-p', str(root), '-c', str(nginx_config), '-g', 'daemon off;'], stdout=stderr, stderr=stderr)
                def ready():
                    try:
                        with socket.create_connection(('127.0.0.1', port), timeout=0.1):
                            return True
                    except OSError:
                        if nginx.poll() is not None:
                            raise RuntimeError(stderr_path.read_text())
                        return False
                wait_for(ready)
                cases = [
                    ('/ok?lang=en', {}, 200),
                    ('/admin/password-reset/reset?email=synthetic%40example.invalid&token=' + TOKEN, {}, 502),
                    ('/admin/password-reset/reset/' + TOKEN, {}, 502),
                    ('/admin/password-reset/reset/%61' + TOKEN[1:], {}, 502),
                    ('/broken?token=' + OTHER_TOKEN, {}, 502),
                    ('/broken?%74oken=' + TOKEN[:20] + '%61' + TOKEN[21:], {}, 502),
                    ('/admin/%70assword%2dreset/reset?%74%6f%6b%65%6e=%61' + TOKEN[1:], {}, 502),
                    ('/admin/password-reset/reset?padding=' + ('x' * 1750) + '&token=' + TOKEN, {}, 502),
                    ('/denied', {'Referer': 'https://preview.jauharat.com/admin/password-reset/reset?token=' + TOKEN}, 403),
                    ('/ok', {'Referer': 'https://example.invalid/path/' + OTHER_TOKEN}, 200),
                    ('/missing-password-reset/reset/' + TOKEN, {}, 404),
                    ('/private-review?token=' + TOKEN, {}, 401),
                    ('/redirect?token=' + TOKEN, {}, 302),
                ]
                for path, headers, expected in cases:
                    connection = http.client.HTTPConnection('127.0.0.1', port, timeout=3)
                    connection.request('GET', path, headers=headers)
                    response = connection.getresponse()
                    assert response.status == expected, 'Unexpected isolated test status.'
                    if path.startswith('/admin/'):
                        assert response.getheader('Referrer-Policy') == 'no-referrer'
                    response.read()
                    connection.close()
                for path in ['/unrelated?lang=en', '/admin/password-reset/reset?token=' + TOKEN]:
                    connection = http.client.HTTPConnection('127.0.0.1', default_port, timeout=3)
                    connection.request('GET', path, headers={'Host': 'unknown.invalid'})
                    response = connection.getresponse()
                    assert response.status == 404
                    response.read()
                    connection.close()
                with socket.create_connection(('127.0.0.1', default_port), timeout=3) as client:
                    client.sendall(('GET /admin/password-reset/reset?token=' + TOKEN + ' HTTP/1.1\r\nBad header: invalid\r\n\r\n').encode())
                    assert b'400' in client.recv(1024)
                with socket.create_connection(('127.0.0.1', port), timeout=3) as client:
                    client.sendall(('GET /admin/password-reset/reset?token=' + TOKEN + ' HTTP/1.1\r\nHost: localhost\r\nBad header: invalid\r\n\r\n').encode())
                    assert b'400' in client.recv(1024)
                # Exercise non-request global diagnostics and unusual error field placement.
                with socket.socket(socket.AF_UNIX, socket.SOCK_DGRAM) as client:
                    for message in [
                        'ordinary worker diagnostic retained',
                        'unquoted credential ' + TOKEN + ' remains redacted',
                        'truncated request="GET /admin/password-reset/reset?token=' + TOKEN[:32],
                        'failed path="/admin/password-reset/reset/SYNTHETIC-NONHEX" referrer="https://example.invalid/?token=SYNTHETIC-OTHER"',
                    ]:
                        client.sendto(('<131>Oct 13 12:00:00 localhost jbr_test: ' + message).encode(), str(socket_path))
                wait_for(lambda: error_file.exists() and 'ordinary worker diagnostic retained' in error_file.read_text())
                assert 'connect() failed' in error_file.read_text(), 'Useful upstream error reason lost.'
                assert 'client: 127.0.0.1' in error_file.read_text(), 'Useful client audit field lost.'
                # Rotation must continue to produce sanitized diagnostics.
                error_file.rename(root / 'errors.rotated.log')
                rsyslog.send_signal(signal.SIGHUP)
                time.sleep(0.1)
                connection = http.client.HTTPConnection('127.0.0.1', port, timeout=3)
                connection.request('GET', '/broken?token=' + TOKEN)
                assert connection.getresponse().status == 502
                connection.close()
                wait_for(error_file.exists)
                # Loss of the error-log collector must not expose a raw fallback URL.
                rsyslog.terminate()
                rsyslog.wait(timeout=5)
                socket_path.unlink(missing_ok=True)
                connection = http.client.HTTPConnection('127.0.0.1', port, timeout=3)
                connection.request('GET', '/broken?token=' + TOKEN)
                assert connection.getresponse().status == 502
                connection.close()
                nginx.send_signal(signal.SIGQUIT)
                nginx.wait(timeout=5)
                rows = [json.loads(line) for line in access_file.read_text().splitlines()]
                assert len(rows) == len(cases) + 3, 'A failure lost its access audit record.'
                assert rows[0]['uri'] == '/ok?lang=en', 'Ordinary request URI was removed.'
                assert all({'time', 'client', 'server', 'method', 'status', 'duration', 'connection'} <= row.keys() for row in rows)
                global_rows = [json.loads(line) for line in global_access_file.read_text().splitlines()]
                assert len(global_rows) == 3
                assert global_rows[0]['uri'] == '/unrelated?lang=en'
                for log in [access_file, global_access_file, error_file, root / 'errors.rotated.log', stderr_path, root / 'raw-fallback.log']:
                    content = log.read_text() if log.exists() else ''
                    assert TOKEN[:32] not in content and OTHER_TOKEN[:32] not in content, 'Synthetic token reached disk in '+str(log.name)
                    assert 'SYNTHETIC-NONHEX' not in content and 'SYNTHETIC-OTHER' not in content
                    assert '%61' + TOKEN[1:] not in content
            finally:
                for process in [nginx, rsyslog]:
                    if process is not None and process.poll() is None:
                        process.terminate()
                        process.wait(timeout=5)
    print('Token-safe logging integration passed: path/query/referrer, 200/302/400/401/403/404/502, rotation and collector failure; synthetic temporary files cleaned.')


if __name__ == '__main__':
    main()
