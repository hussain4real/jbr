<div
    x-data="{
        disposed: false, minutes: 60, pending: false, url: '', expiresAt: '', message: '', copied: false,
        async generate() {
            if (this.pending || this.url) return;
            this.pending = true;
            this.message = '';
            try {
                const result = await $wire.generateSetupLink(@js($targetId), Number(this.minutes));
                if (this.disposed) return;
                if (!result?.url) {
                    this.message = 'Unable to generate a link. If a link was just issued, wait one minute and reopen this dialog. Otherwise refresh to check account access.';
                    return;
                }
                this.url = result.url;
                this.expiresAt = new Date(result.expiresAt).toLocaleString();
                await this.copy();
            } catch {
                this.message = 'Unable to generate a link. Refresh to check account access before trying again.';
            } finally {
                this.pending = false;
            }
        },
        async copy() {
            if (this.disposed || !this.url) return;
            this.copied = false;
            try {
                await navigator.clipboard.writeText(this.url);
                if (this.disposed) return;
                this.copied = true;
                this.message = 'Setup link copied. Share it privately with this account holder.';
            } catch {
                this.message = 'Clipboard access was blocked. Use Copy link again or select the link and copy it manually.';
            }
        },
        destroy() { this.disposed = true; this.url = ''; }
    }"
    x-on:modal-closed.window="if ($event.detail.id === $el.closest('[data-fi-modal-id]')?.dataset.fiModalId) destroy()"
    x-on:close-modal-quietly.window="if ($event.detail.id === $el.closest('[data-fi-modal-id]')?.dataset.fiModalId) destroy()"
    style="display: grid; gap: 1rem"
>
    <style>.jbr-setup-link-group { display: grid; gap: 1rem; }</style>
    <p>Set up <strong>{{ $email }}</strong> without sending email. This replaces any earlier password setup or reset link. The link works once; authenticator verification is still required.</p>
    <div x-show="!url" class="jbr-setup-link-group">
        <label for="setup-expiry-{{ $targetId }}" style="display: block">Link lifetime</label>
        <x-filament::input.wrapper>
            <x-filament::input.select id="setup-expiry-{{ $targetId }}" x-model="minutes" x-bind:disabled="pending">
                <option value="60">1 hour (recommended)</option>
                <option value="900">15 hours — extended setup window</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
        <p x-show="Number(minutes) === 900">Anyone holding this link can set the password for up to 15 hours. Choose this only when the account holder needs more time.</p>
        <x-filament::button type="button" x-on:click="generate()" x-bind:disabled="pending">
            <span x-text="pending ? 'Generating…' : 'Generate and copy setup link'"></span>
        </x-filament::button>
    </div>
    <div x-show="url" x-cloak class="jbr-setup-link-group">
        <p>Expires <strong x-text="expiresAt"></strong> (your local time).</p>
        <label for="setup-url-{{ $targetId }}" style="display: block">Private setup link</label>
        <x-filament::input.wrapper>
            <x-filament::input id="setup-url-{{ $targetId }}" type="text" readonly x-bind:value="url" x-on:click="$el.select()" autocomplete="off" />
        </x-filament::input.wrapper>
        <x-filament::button type="button" x-on:click="copy()">
            <span x-text="copied ? 'Copy link again' : 'Copy link'"></span>
        </x-filament::button>
        <p>Closing clears the link from this dialog. To replace it, reopen this action after one minute. The previous link will stop working.</p>
    </div>
    <p role="status" aria-live="polite" x-text="message"></p>
</div>
