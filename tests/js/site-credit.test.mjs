import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile, writeFile, unlink } from 'node:fs/promises';
import { parse, compileScript } from '@vue/compiler-sfc';
import { createSSRApp } from 'vue';
import { renderToString } from '@vue/server-renderer';

const file = new URL('../../resources/js/SiteCredit.vue', import.meta.url);
const { descriptor } = parse(await readFile(file, 'utf8'));
const compiled = compileScript(descriptor, { id: 'site-credit-test', inlineTemplate: true });
const modulePath = new URL('./compiled-credit.mjs', import.meta.url);
await writeFile(modulePath, compiled.content);
let SiteCredit;
try {
    SiteCredit = (await import(modulePath.href)).default;
} finally {
    await unlink(modulePath);
}
const credit = {
    enabled: true,
    label: 'Designed, Developed and Hosted by',
    name: 'Better Web Design and Development Services, LLC',
    url: 'https://www.bwd.io',
    title: 'Click to visit bwd.io and learn more about Better Web Design and Development Services',
    theme: 'inherit',
};
const render = (data = credit, extra = {}) => renderToString(createSSRApp(SiteCredit, { credit: data, ...extra }));

test('Vue preserves the original link and escapes text without adding a second footer', async () => {
    const html = await render({ ...credit, name: '<img src=x onerror=bad()>' }, { class: 'host-class' });
    assert.match(html, /href="https:\/\/www\.bwd\.io"/);
    assert.match(html, /&lt;img src=x onerror=bad\(\)&gt;/);
    assert.match(html, /host-class/);
    assert.match(html, /data-theme="inherit"/);
    assert.doesNotMatch(html, /<img|<footer|target=/);
});

test('Vue omits disabled and unsafe destinations', async () => {
    for (const data of [
        { ...credit, enabled: false },
        { ...credit, url: 'javascript:alert(1)' },
        { ...credit, url: 'data:text/html,bad' },
        { ...credit, url: '//example.com' },
    ]) {
        assert.doesNotMatch(await render(data), /<a|bwd-site-credit/);
    }
});

test('Vue theme overrides are isolated and invalid themes use inheritance', async () => {
    assert.match(await render(credit, { theme: 'dark' }), /data-theme="dark"/);
    assert.match(await render({ ...credit, theme: 'light' }), /data-theme="light"/);
    assert.match(await render(credit, { theme: 'other' }), /data-theme="inherit"/);
    assert.equal(credit.theme, 'inherit');
});
