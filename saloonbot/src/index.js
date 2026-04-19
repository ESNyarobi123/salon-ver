// Node 18: Baileys calls bare `crypto.subtle` (Web Crypto). Node 19+ sets global crypto; polyfill for 18.x.
(function initWebCryptoGlobal() {
    const { webcrypto } = require('node:crypto');
    if (!webcrypto) {
        return;
    }
    if (typeof globalThis.crypto === 'undefined') {
        globalThis.crypto = webcrypto;
    }
    if (typeof global.crypto === 'undefined') {
        global.crypto = webcrypto;
    }
})();

const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
} = require('@whiskeysockets/baileys');
const { Boom } = require('@hapi/boom');
const pino = require('pino');
const qrcode = require('qrcode-terminal');
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env') });

const { handleMessage } = require('./handler.js');

console.log('╔════════════════════════════════════════════════════════════════╗');
console.log('║ TipTap — Saloon WhatsApp / WhatsApp ya salon (Baileys)        ║');
console.log('║ Bookings, huduma, rejareja & malipo / services & payments      ║');
console.log('╚════════════════════════════════════════════════════════════════╝');
console.log('');

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
    const { version, isLatest } = await fetchLatestBaileysVersion();

    console.log(`📱 WhatsApp Web v${version.join('.')} (isLatest: ${isLatest})`);
    console.log(`🌐 API Base URL / URL ya API: ${process.env.API_BASE_URL}`);
    console.log(`🔑 BOT_TOKEN: ${process.env.BOT_TOKEN ? 'Imepakiwa / loaded (anza na / starts ' + process.env.BOT_TOKEN.substring(0, 5) + '...)' : 'Haijapakiwa / not loaded'}`);
    console.log('');

    const sock = makeWASocket({
        version,
        logger: pino({ level: 'silent' }),
        printQRInTerminal: false,
        auth: state,
        getMessage: async (key) => {
            return { conversation: 'hello' };
        }
    });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('');
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.log('📲 Changanua QR hapa chini na WhatsApp (Vifaa vilivyounganishwa > Unganisha kifaa)');
            console.log('   EN: Scan QR below — WhatsApp > Linked Devices > Link a Device');
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            qrcode.generate(qr, { small: true });
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        }

        if (connection === 'close') {
            const statusCode = (lastDisconnect?.error instanceof Boom)
                ? lastDisconnect.error.output.statusCode
                : null;

            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            console.log('');
            console.log(`❌ Muunganisho umefungwa / Connection closed. Sababu: ${lastDisconnect?.error?.message || 'Unknown'}`);

            if (shouldReconnect) {
                console.log('🔄 Inajaribu tena baada ya sekunde 3... / Reconnecting in 3 seconds...');
                setTimeout(() => connectToWhatsApp(), 3000);
            } else {
                console.log('🚪 Umetoka. Futa folda auth_info_baileys kisha anzisha tena.');
                console.log('   EN: Logged out. Delete auth_info_baileys folder and restart.');
            }
        } else if (connection === 'open') {
            console.log('');
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.log('✅ Bot ya TipTap iko mtandaoni na tayari kwa ujumbe! / ONLINE — ready for messages.');
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.log('');
        }
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('messages.upsert', async (m) => {
        if (m.type === 'notify') {
            for (const msg of m.messages) {
                if (!msg.key.fromMe && msg.message) {
                    const from = msg.key.remoteJid;
                    const msgType = Object.keys(msg.message)[0];

                    console.log(`📩 [${new Date().toLocaleTimeString()}] Ujumbe kutoka / from ${from}: ${msgType}`);

                    try {
                        await handleMessage(sock, msg);
                    } catch (error) {
                        console.error('❌ Hitilafu wakati wa kushughulikia ujumbe / Error handling message:', error);
                    }
                }
            }
        }
    });

    // Handle graceful shutdown
    process.on('SIGINT', () => {
        console.log('\n👋 Kizimwa / Shutting down TipTap...');
        process.exit(0);
    });
}

// Start the bot
connectToWhatsApp().catch(err => {
    console.error('❌ Imeshindwa kuanzisha bot / Failed to start bot:', err);
    process.exit(1);
});
