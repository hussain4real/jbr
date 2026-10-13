import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';

const blade = readFileSync(
    new URL(
        '../../resources/views/filament/users/copy-setup-link.blade.php',
        import.meta.url,
    ),
    'utf8',
);
const source = blade
    .match(/x-data="([\s\S]*?)"\n/)[1]
    .replace('@js($targetId)', '7');
const closeHandler = blade.match(/x-on:modal-closed.window="([^"]+)"/)[1];
const fixture = {
    url: 'https://preview.example.invalid/setup?token=local-fixture',
    expiresAt: '2026-10-14T01:00:00Z',
};

function component(generateSetupLink, writeText) {
    return vm.runInNewContext(`(${source})`, {
        $wire: { generateSetupLink },
        navigator: { clipboard: { writeText } },
        Date,
    });
}

function close(data, id) {
    const element = {
        closest: () => ({ dataset: { fiModalId: 'fixture-modal' } }),
    };
    vm.runInNewContext(closeHandler, {
        $event: { detail: { id } },
        $el: element,
        destroy: () => data.destroy(),
    });
}

await test('double generation shares one pending request and copies once', async () => {
    let resolve;
    let calls = 0;
    let writes = 0;
    const data = component(
        () => {
            calls++;
            return new Promise((done) => {
                resolve = done;
            });
        },
        async () => {
            writes++;
        },
    );
    const pending = data.generate();
    await data.generate();
    assert.equal(calls, 1);
    assert.equal(data.pending, true);
    resolve(fixture);
    await pending;
    assert.equal(writes, 1);
    assert.equal(data.copied, true);
    assert.equal(data.pending, false);
    await data.generate();
    assert.equal(calls, 1);
});

await test('matching modal closure prevents copying a delayed response and clears the link', async () => {
    let resolve;
    let writes = 0;
    const data = component(
        () =>
            new Promise((done) => {
                resolve = done;
            }),
        async () => {
            writes++;
        },
    );
    const pending = data.generate();
    close(data, 'another-modal');
    assert.equal(data.disposed, false);
    close(data, 'fixture-modal');
    resolve(fixture);
    await pending;
    assert.equal(writes, 0);
    assert.equal(data.url, '');
    assert.equal(data.pending, false);
});

await test('clipboard failure preserves the link for manual copy and a retry copies the same link', async () => {
    let blocked = true;
    const writes = [];
    const data = component(
        async () => fixture,
        async (url) => {
            if (blocked) throw new Error('denied');
            writes.push(url);
        },
    );
    await data.generate();
    assert.equal(data.url, fixture.url);
    assert.equal(data.copied, false);
    assert.match(data.message, /Clipboard access was blocked/);
    blocked = false;
    await data.copy();
    assert.deepEqual(writes, [fixture.url]);
    close(data, 'fixture-modal');
    assert.equal(data.url, '');
});

await test('failed generation gives a safe message and permits retry', async () => {
    let failing = true;
    const data = component(
        async () => {
            if (failing) throw new Error('private backend detail');
            return fixture;
        },
        async () => {},
    );
    await data.generate();
    assert.equal(data.url, '');
    assert.equal(data.pending, false);
    assert.equal(data.message.includes('private backend detail'), false);
    failing = false;
    await data.generate();
    assert.equal(data.copied, true);
});
