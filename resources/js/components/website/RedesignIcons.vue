<template>
    <svg width="0" height="0" style="position: absolute" aria-hidden="true">
        <symbol id="i-arrow" viewBox="0 0 24 24">
            <path
                d="M4 12h15m-6-6 6 6-6 6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-left" viewBox="0 0 24 24">
            <path
                d="M20 12H5m6-6-6 6 6 6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-x" viewBox="0 0 24 24">
            <path
                d="M5 5l14 14M19 5 5 19"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
            />
        </symbol>
        <symbol id="i-menu" viewBox="0 0 24 24">
            <path
                d="M3 8h18M3 16h18"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
            />
        </symbol>
        <symbol id="i-grid" viewBox="0 0 24 24">
            <path
                d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
            />
        </symbol>
        <symbol id="i-bed" viewBox="0 0 24 24">
            <path
                d="M3 18V7m0 7h18v4M21 14v-2a3 3 0 0 0-3-3h-7v5M7 11.5a1.5 1.5 0 1 0 0-.01"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-school" viewBox="0 0 24 24">
            <path
                d="M2 9l10-5 10 5-10 5L2 9Zm4 2.5V16c0 1.5 3 3 6 3s6-1.5 6-3v-4.5M22 9v6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-team" viewBox="0 0 24 24">
            <circle
                cx="12"
                cy="12"
                r="9"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
            />
            <path
                d="M12 7l4 3-1.5 4.5h-5L8 10l4-3ZM12 3v4M21 10.5l-5-.5M3 10.5l5-.5M8.5 20l1-5.5M15.5 20l-1-5.5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.4"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-pool" viewBox="0 0 24 24">
            <path
                d="M2 17c2 0 2-1.2 4-1.2s2 1.2 4 1.2 2-1.2 4-1.2 2 1.2 4 1.2 2-1.2 4-1.2M2 21c2 0 2-1.2 4-1.2s2 1.2 4 1.2 2-1.2 4-1.2 2 1.2 4 1.2 2-1.2 4-1.2M8 14V5a2 2 0 0 1 4 0M16 14V5a2 2 0 0 0-4 0M8 8h8M8 11.5h8"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
            />
        </symbol>
        <symbol id="i-stadium" viewBox="0 0 24 24">
            <ellipse
                cx="12"
                cy="12"
                rx="10"
                ry="6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
            />
            <ellipse
                cx="12"
                cy="12"
                rx="5"
                ry="2.6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.4"
            />
            <path d="M12 6v12" stroke="currentColor" stroke-width="1.4" />
        </symbol>
        <symbol id="i-dune" viewBox="0 0 24 24">
            <path
                d="M2 18c3-5 6-7 9-7s5 3 11 7M7 13.5c2-3 4-5.5 7-5.5s4 2 6 4"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
            />
            <circle
                cx="18"
                cy="5"
                r="2"
                fill="none"
                stroke="currentColor"
                stroke-width="1.4"
            />
        </symbol>
        <symbol id="i-clock" viewBox="0 0 24 24">
            <circle
                cx="12"
                cy="12"
                r="9"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
            />
            <path
                d="M12 7v5l3.5 2"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
            />
        </symbol>
        <symbol id="i-wa" viewBox="0 0 24 24">
            <path
                d="M4 20l1.2-3.8A8 8 0 1 1 8 19l-4 1Z"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linejoin="round"
            />
            <path
                d="M9 9.5c.3 2 2.2 4.2 5 5l1.2-1.3-1.8-1-1 .8c-.8-.4-1.6-1.2-2-2l.8-1-1-1.8L9 9.5Z"
                fill="currentColor"
            />
        </symbol>
        <symbol id="i-tent" viewBox="0 0 24 24">
            <path
                d="M2 20h20M3.5 20 12 5l8.5 15M12 5v15M8 20l4-6.5 4 6.5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.1"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-dallah" viewBox="0 0 24 24">
            <path
                d="M9 3.5h6M10.5 3.5v2.2C7.6 7 6.5 9.6 6.5 12.6V18h11v-5.4c0-3-1.1-5.6-4-6.9V3.5M17.5 12.5c2-.8 3.2-2.8 3.4-5.3M5 18h14M7.5 20.5h9"
                fill="none"
                stroke="currentColor"
                stroke-width="1.1"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </symbol>
        <symbol id="i-dunes2" viewBox="0 0 24 24">
            <path
                d="M1.5 18.5c3.5-5 6.5-7 9.5-7s5.5 3 11.5 7M6 14c2-3.2 4.2-5.8 7.2-5.8 2.4 0 4 1.6 6.3 3.8"
                fill="none"
                stroke="currentColor"
                stroke-width="1.1"
                stroke-linecap="round"
            />
            <circle
                cx="18"
                cy="5"
                r="2"
                fill="none"
                stroke="currentColor"
                stroke-width="1.1"
            />
        </symbol>
    </svg>
</template>
