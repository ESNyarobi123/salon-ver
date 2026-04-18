const api = require('./api');
const { T, TRepl } = require('./lang');

const sessions = {};

// ═══════════════════════════════════════════════════════════════
// MAIN MESSAGE HANDLER
// ═══════════════════════════════════════════════════════════════
async function handleMessage(sock, msg) {
    const from = msg.key.remoteJid;

    if (from.endsWith('@g.us') || from === 'status@broadcast') {
        return;
    }

    let text = extractMessageText(msg);
    if (!text) return;

    if (!sessions[from]) {
        sessions[from] = createNewSession();
    }

    const session = sessions[from];
    if (msg.pushName) session.customer_name = msg.pushName;
    console.log(`📩 [${session.state}] From: ${from} | Text: "${text}"`);

    // ═══════════════════════════════════════════════════════════════
    // SMART MENU MAPPING (Middleware)
    // ═══════════════════════════════════════════════════════════════
    if (session.menu_options && session.menu_options[text.toLowerCase()]) {
        const mappedAction = session.menu_options[text.toLowerCase()];
        text = mappedAction;
    } else if (session.menu_options && !isNaN(text)) {
        const num = parseInt(text).toString();
        if (session.menu_options[num]) {
            text = session.menu_options[num];
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // GLOBAL: 0 = Back to main menu (from any state)
    // ═══════════════════════════════════════════════════════════════
    if (text === '0') {
        session.state = 'HOME';
        await showHomeScreen(sock, from, session);
        return;
    }

    // ═══════════════════════════════════════════════════════════════
    // GLOBAL COMMANDS
    // ═══════════════════════════════════════════════════════════════
    const cmd = text.toLowerCase();
    if (cmd === '!waiter' || cmd === '!stylist') {
        if (session.waiter_name) {
            await sendText(sock, from, TRepl(session, 'stylist_cmd_reply', { name: session.waiter_name }));
        } else {
            await sendText(sock, from, T(session, 'stylist_cmd_none'));
        }
        return;
    }

    if (cmd === '!status' || cmd === 'status') {
        if (session.restaurant_id && session.table_number) {
            return await showTrackStatus(sock, from, session);
        } else {
            await sendText(sock, from, T(session, 'status_need_context'));
            return;
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // ENTRY POINT: QR CODES & TAGS (Unified)
    // ═══════════════════════════════════════════════════════════════
    // Check for START command or Tag format (e.g., SMK-W01)
    const isTag = /^[A-Z0-9]+-[A-Z0-9]+$/i.test(text);
    if (text.startsWith('START|') || text.startsWith('START_') || isTag) {
        return await handleEntry(sock, from, session, text);
    }

    // ═══════════════════════════════════════════════════════════════
    // STATE MACHINE
    // ═══════════════════════════════════════════════════════════════
    try {
        switch (session.state) {
            case 'START':
                await handleStartState(sock, from, session, text);
                break;

            case 'SEARCH_RESTAURANT':
                await handleSearchState(sock, from, session, text);
                break;

            case 'PICK_TABLE':
            case 'TABLE_INPUT':
                await handleTableState(sock, from, session, text);
                break;

            case 'HOME':
                await handleHomeState(sock, from, session, text);
                break;

            case 'MENU_HUB':
                await handleMenuHubState(sock, from, session, text);
                break;

            case 'CATEGORIES':
                await handleCategoriesState(sock, from, session, text);
                break;

            case 'ITEMS_LIST':
                await handleItemsListState(sock, from, session, text);
                break;

            case 'ITEM_DETAIL':
                await handleItemDetailState(sock, from, session, text);
                break;

            case 'QUANTITY':
            case 'QUANTITY_MORE':
                await handleQuantityState(sock, from, session, text);
                break;

            case 'CART':
                await handleCartState(sock, from, session, text);
                break;

            case 'CART_EDIT':
                await handleCartEditState(sock, from, session, text);
                break;

            case 'CONFIRM_ORDER':
                await handleConfirmOrderState(sock, from, session, text);
                break;

            case 'PAYMENT_SUMMARY':
                await handlePaymentSummaryState(sock, from, session, text);
                break;

            case 'CASH_PAYMENT':
                await handleCashPaymentState(sock, from, session, text);
                break;

            case 'PROVIDER_SELECT':
                await handleProviderSelectState(sock, from, session, text);
                break;

            case 'USSD_NUMBER':
                await handleUssdNumberState(sock, from, session, text);
                break;

            case 'PAY_NOW':
                await handlePayNowState(sock, from, session, text);
                break;

            case 'USSD_PENDING':
                await handleUssdPendingState(sock, from, session, text);
                break;

            case 'MANUAL_USSD':
                await handleManualUssdState(sock, from, session, text);
                break;

            case 'TRACK_STATUS':
                await handleTrackStatusState(sock, from, session, text);
                break;

            case 'FEEDBACK_TYPE':
            case 'FEEDBACK_WAITER_LIST':
            case 'FEEDBACK':
            case 'FEEDBACK_B':
                await handleFeedbackState(sock, from, session, text);
                break;

            case 'FEEDBACK_COMMENT':
                await handleFeedbackCommentState(sock, from, session, text);
                break;

            case 'TIP':
                await handleTipState(sock, from, session, text);
                break;

            case 'CALL_WAITER':
                await handleCallWaiterState(sock, from, session, text);
                break;

            case 'WAITERS_LIST':
                await handleWaitersListState(sock, from, session, text);
                break;

            case 'MENU_SELECTION':
                await handleMenuSelectionState(sock, from, session, text);
                break;

            case 'MENU_IMAGE_ORDER':
                await handleMenuImageOrderState(sock, from, session, text);
                break;

            case 'QUICK_PAYMENT_AMOUNT':
                await handleQuickPaymentAmountState(sock, from, session, text);
                break;

            case 'QUICK_PAYMENT_PHONE':
                await handleQuickPaymentPhoneState(sock, from, session, text);
                break;

            case 'QUICK_PAYMENT_NETWORK':
                await handleQuickPaymentNetworkState(sock, from, session, text);
                break;

            case 'QUICK_PAYMENT_PENDING':
                await handleQuickPaymentPendingState(sock, from, session, text);
                break;

            case 'CALL_WAITER_ASK_TABLE':
                await handleCallWaiterAskTableState(sock, from, session, text);
                break;

            case 'PICK_TABLE_FOR_ORDER':
                await handlePickTableForOrderState(sock, from, session, text);
                break;

            case 'LANGUAGE_SELECT':
                await handleLanguageSelectState(sock, from, session, text);
                break;

            case 'SELECT_WAITER_TIP':
                await handleSelectWaiterTipState(sock, from, session, text);
                break;

            case 'TIP_AMOUNT':
                await handleTipAmountState(sock, from, session, text);
                break;

            default:
                await sendText(sock, from, T(session, 'not_understood'));
                session.state = 'START';
                break;
        }
    } catch (error) {
        console.error('Handler error:', error);
        await sendText(sock, from, '❌ Technical error. Please try again.');
    }
}

// ═══════════════════════════════════════════════════════════════
// MESSAGE EXTRACTION
// ═══════════════════════════════════════════════════════════════
function extractMessageText(msg) {
    const m = msg.message;
    if (!m) return null;

    // Regular text
    if (m.conversation) return m.conversation;
    if (m.extendedTextMessage?.text) return m.extendedTextMessage.text;

    // Button response
    if (m.buttonsResponseMessage?.selectedButtonId) {
        return m.buttonsResponseMessage.selectedButtonId;
    }

    // List response
    if (m.listResponseMessage?.singleSelectReply?.selectedRowId) {
        return m.listResponseMessage.singleSelectReply.selectedRowId;
    }

    // Template button response
    if (m.templateButtonReplyMessage?.selectedId) {
        return m.templateButtonReplyMessage.selectedId;
    }

    // Interactive response (new format)
    if (m.interactiveResponseMessage) {
        const body = m.interactiveResponseMessage.nativeFlowResponseMessage?.paramsJson;
        if (body) {
            try {
                const parsed = JSON.parse(body);
                return parsed.id || parsed.flow_token;
            } catch (e) { }
        }
    }

    return null;
}

// ═══════════════════════════════════════════════════════════════
// SESSION MANAGEMENT
// ═══════════════════════════════════════════════════════════════
function createNewSession() {
    return {
        state: 'START',
        lang: 'en',
        cart: [],
        restaurant_id: null,
        restaurant_name: null,
        support_phone: null,
        table_number: null,
        table_id: null,
        waiter_id: null,
        waiter_name: null,
        customer_name: null,
        active_order_id: null,
        order_total: 0,
        menu_cache: null,
        current_category: null,
        ussd_phone: null,
        ussd_provider: null,
        rating: null,
        pending_item: null,
        pending_qty: 1,
        quick_payment_id: null,
        quick_payment_amount: null,
        quick_payment_desc: null,
        quick_payment_mode: null,
        quick_payment_network: null,
        tip_waiter_id: null,
        tip_waiter_name: null,
        feedback_waiter_id: null,
        feedback_waiter_name: null
    };
}

// ═══════════════════════════════════════════════════════════════
// UNIFIED ENTRY HANDLER (QR & TAGS)
// ═══════════════════════════════════════════════════════════════
async function handleEntry(sock, from, session, text) {
    await sendText(sock, from, `🔄 ${T(session, 'verifying')}`);

    try {
        const result = await api.parseEntry(text);
        console.log('🔍 Parse Entry Result:', JSON.stringify(result, null, 2));

        if (result.type === 'waiter') {
            // Waiter Assignment
            session.restaurant_id = result.data.restaurant_id;
            session.restaurant_name = result.data.restaurant_name;
            session.support_phone = result.data.support_phone || null;
            session.waiter_id = result.data.waiter_id;
            session.waiter_name = result.data.waiter_name;
            session.header_info = result.data.waiter_name; // Set header for Home Screen

            // Do not send standalone "Welcome to X! Y will be your waiter." — go straight to menu only.
            // (API may send skip_standalone_welcome; we always skip here so the first bubble never appears.)

            // If we don't have a table yet, maybe ask for it or just go home?
            // Assuming we go to Home, but without a table number some features might be limited.
            // However, the user didn't specify asking for a table after waiter scan.
            // Let's go to Home.
            await showHomeScreen(sock, from, session);

        } else if (result.type === 'table') {
            // Table Assignment
            session.restaurant_id = result.data.restaurant_id;
            session.restaurant_name = result.data.restaurant_name;
            session.support_phone = result.data.support_phone || null;
            session.table_id = result.data.table_id;
            session.table_number = result.data.table_number || result.data.table_name; // Assuming 'number' is the display number
            session.header_info = `${T(session, 'table')} ${session.table_number}`; // Seat label for home header

            // Do not send standalone welcome line — go straight to menu only.
            await showHomeScreen(sock, from, session);

} else {
            await sendText(sock, from,
                `❌ ${T(session, 'invalid_qr')}\n\n${T(session, 'invalid_entry_help')}`
            );
            session.state = 'SEARCH_RESTAURANT';
        }
    } catch (error) {
        console.error('Entry error:', error);
        await sendText(sock, from, `❌ ${T(session, 'tech_error')}`);
        session.state = 'SEARCH_RESTAURANT';
    }
}

// ═══════════════════════════════════════════════════════════════
// STATE HANDLERS
// ═══════════════════════════════════════════════════════════════

async function handleStartState(sock, from, session, text) {
    const greetings = ['hi', 'hello', 'mambo', 'habari', 'niaje', 'sasa', 'hujambo'];
    if (greetings.includes(text.toLowerCase())) {
        await sendText(sock, from, T(session, 'welcome_tiptap'));
        session.state = 'SEARCH_RESTAURANT';
    } else {
        await handleSearchRestaurant(sock, from, session, text);
    }
}

async function handleSearchState(sock, from, session, text) {
    // Handle numbered selection
    const selection = parseInt(text);
    if (!isNaN(selection) && session.search_results) {
        if (selection === 0) {
            await sendText(sock, from, T(session, 'search_type_saloon'));
            return;
        }

        const restaurant = session.search_results[selection - 1];
        if (restaurant) {
            session.restaurant_id = restaurant.id;
            session.restaurant_name = restaurant.name;
            session.support_phone = restaurant.support_phone || null;

            if (!session.table_number) {
                await showTableSelection(sock, from, session);
                session.state = 'PICK_TABLE';
            } else {
                await showHomeScreen(sock, from, session);
            }
            return;
        }
    }

    if (text.startsWith('pick_rest_')) {
        session.restaurant_id = text.replace('pick_rest_', '');
        try {
            const result = await api.verifyRestaurant(session.restaurant_id, null);
            if (result.success) {
                session.restaurant_name = result.data.name;
                session.support_phone = result.data.support_phone || null;
            }
        } catch (e) { }

        if (!session.table_number) {
            await showTableSelection(sock, from, session);
            session.state = 'PICK_TABLE';
        } else {
            await showHomeScreen(sock, from, session);
        }
    } else if (text === 'search_again') {
        await sendText(sock, from, T(session, 'search_type_saloon'));
    } else {
        await handleSearchRestaurant(sock, from, session, text);
    }
}

async function handleTableState(sock, from, session, text) {
    if (text.startsWith('table_')) {
        const val = text.replace('table_', '');
        if (val === 'type') {
            session.state = 'TABLE_INPUT';
            await sendText(sock, from, T(session, 'enter_table'));
        } else {
            session.table_number = val;
            await showHomeScreen(sock, from, session);
        }
    } else if (!isNaN(text) && parseInt(text) > 0) {
        session.table_number = text;
        await showHomeScreen(sock, from, session);
    } else {
        await sendText(sock, from, T(session, 'valid_table'));
    }
}

async function handleHomeState(sock, from, session, text) {
    const t = text.toLowerCase();

    // New Menu Options Mapping
    // Menu = menu image only (no list menu from main screen)
    if (t === 'view_menu' || t.includes('menu')) {
        await showMenuImage(sock, from, session);
        return;
    }
    if (t === 'track_order' || t === 'status' || t.includes('track')) {
        await showTrackStatus(sock, from, session);
    } else if (t === 'rate_service' || t.includes('rate')) {
        if (session.waiter_id && session.waiter_name) {
            session.feedback_waiter_id = session.waiter_id;
            session.feedback_waiter_name = session.waiter_name;
            await showFeedbackA(sock, from, session);
        } else {
            await showFeedbackTypeSelection(sock, from, session);
        }
    } else if (t === 'live_bill' || t.includes('bill') || t.includes('lipa')) {
        await showLiveBillOptions(sock, from, session);
    } else if (t === 'give_tips' || t.includes('tip')) {
        if (session.waiter_id && session.waiter_name) {
            // Auto-select assigned waiter
            session.tip_waiter_id = session.waiter_id;
            session.tip_waiter_name = session.waiter_name;
            session.quick_payment_mode = 'tip';
            session.quick_payment_desc = `Tip for ${session.tip_waiter_name}`;
            await showQuickPaymentAmount(sock, from, session);
        } else {
            await showWaiterTipList(sock, from, session);
        }
    } else if (t === 'call_waiter' || t.includes('call')) {
        if (session.waiter_id) {
            // Check waiter online status FIRST (before asking table) when customer has specific waiter (e.g. from QR)
            let statusRes;
            try {
                statusRes = await api.getWaiterStatus(session.waiter_id);
            } catch (e) {
                console.error('Waiter status check error:', e);
            }
            if (statusRes && statusRes.success && statusRes.data && !statusRes.data.is_online) {
                const waiterName = statusRes.data.name || session.waiter_name || T(session, 'stylist_label');
                const msg = T(session, 'waiter_offline_msg').replace(/{name}/g, waiterName);
                await sendText(sock, from, `⚠️ ${msg}`);
                return;
            }
            if (session.table_number || session.table_id) {
                await initiateCallWaiter(sock, from, session, 'call_waiter', T(session, 'call_stylist_label'));
            } else {
                session.state = 'CALL_WAITER_ASK_TABLE';
                session.pending_call_type = 'call_waiter';
                session.pending_call_label = T(session, 'call_stylist_label');
                await showCallWaiterAskTable(sock, from, session);
            }
        } else {
            await showWaitersList(sock, from, session);
        }
    } else if (t === 'guest_wifi' || t.includes('wifi') || t.includes('wi-fi')) {
        if (!session.restaurant_id) {
            await sendText(sock, from, T(session, 'guest_wifi_no_session'));
            return;
        }
        try {
            const res = await api.getGuestWifi(session.restaurant_id);
            if (res.success && res.data) {
                const ssid = res.data.wifi_ssid;
                const pass = res.data.wifi_password;
                if (!ssid && !pass) {
                    let msg = `📶 *${T(session, 'guest_wifi_title')}*\n\n${T(session, 'guest_wifi_not_set')}`;
                    if (session.support_phone) {
                        msg += `\n\n${T(session, 'support_call')} *${session.support_phone}*`;
                    }
                    msg += `\n\n_${T(session, 'support_type_zero')}_`;
                    await sendText(sock, from, msg);
                } else {
                    let msg = `📶 *${T(session, 'guest_wifi_title')}*\n\n`;
                    if (ssid) {
                        msg += `${T(session, 'guest_wifi_ssid_label')} *${ssid}*\n`;
                    }
                    if (pass) {
                        msg += `${T(session, 'guest_wifi_password_label')} *${pass}*\n`;
                    }
                    if (session.support_phone) {
                        msg += `\n${T(session, 'support_call')} *${session.support_phone}*`;
                    }
                    msg += `\n\n_${T(session, 'support_type_zero')}_`;
                    await sendText(sock, from, msg);
                }
            } else {
                await sendText(sock, from, T(session, 'guest_wifi_error'));
            }
        } catch (e) {
            console.error('Guest WiFi fetch error:', e);
            await sendText(sock, from, T(session, 'guest_wifi_error'));
        }
    } else if (t === 'change_language' || t.includes('language') || t.includes('lugha')) {
        await showLanguageSelect(sock, from, session);
    } else if (t === 'exit_bot' || t.includes('exit')) {
        sessions[from] = createNewSession();
        await sendText(sock, from, T(session, 'goodbye'));
    } else {
        await showHomeScreen(sock, from, session);
    }
}

async function handleCallWaiterState(sock, from, session, text) {
    if (text === 'call_only') {
        await initiateCallWaiter(sock, from, session, 'call_waiter', T(session, 'call_stylist_label'));
    } else if (text === 'request_bill') {
        await initiateCallWaiter(sock, from, session, 'request_bill', T(session, 'request_bill_label'));
    } else if (text === 'list_waiters') {
        await showWaitersList(sock, from, session);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    } else {
        await showCallWaiterOptions(sock, from, session);
    }
}

async function handleWaitersListState(sock, from, session, text) {
    if (text.startsWith('call_waiter_')) {
        const rest = text.replace('call_waiter_', '');
        const pipe = rest.indexOf('|');
        const waiterId = pipe >= 0 ? rest.slice(0, pipe) : null;
        const waiterName = pipe >= 0 ? rest.slice(pipe + 1) : rest;
        if (waiterId) session.waiter_id = waiterId;
        if (waiterName) session.waiter_name = waiterName;
        const apiType = pipe >= 0 ? 'call_waiter' : `call_waiter_${waiterName}`;
        await initiateCallWaiter(sock, from, session, apiType, waiterName ? `${T(session, 'call_stylist_label')}: ${waiterName}` : T(session, 'call_stylist_label'));
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    } else {
        await showWaitersList(sock, from, session);
    }
}

async function showCallWaiterAskTable(sock, from, session) {
    try {
        const result = await api.getRestaurantTables(session.restaurant_id);
        if (result.success && result.data && result.data.length > 0) {
            session.call_waiter_tables = result.data;
            session.menu_options = {};
            let msg = `🪑 *${T(session, 'order_which_table')}*\n\n${T(session, 'choose')}\n`;
            result.data.slice(0, 10).forEach((t, i) => {
                const num = (i + 1).toString();
                session.menu_options[num] = `table_${t.id}`;
                msg += `${num}. ${t.name}\n`;
            });
            msg += `\n_${T(session, 'order_reply_table_number')}_`;
            await sendText(sock, from, msg);
        } else {
            await sendText(sock, from, `🪑 ${T(session, 'order_which_table')}\n\n${T(session, 'enter_table')}`);
        }
    } catch (e) {
        console.error('getRestaurantTables error:', e);
        await sendText(sock, from, `🪑 ${T(session, 'order_which_table')}\n\n${T(session, 'enter_table')}`);
    }
}

/** Ask which table (for order) — tables fetched from manager via API. Used before submitting text order or cart. */
async function showOrderTableSelect(sock, from, session) {
    session.state = 'PICK_TABLE_FOR_ORDER';
    try {
        const result = await api.getRestaurantTables(session.restaurant_id);
        if (result.success && result.data && result.data.length > 0) {
            session.order_tables = result.data;
            session.menu_options = {};
            let msg = `🪑 *${T(session, 'order_which_table')}*\n\n`;
            result.data.slice(0, 15).forEach((t, i) => {
                const num = (i + 1).toString();
                session.menu_options[num] = `table_${t.id}`;
                msg += `${getNumberEmoji(i + 1)} ${t.name}\n`;
            });
            msg += `\n_${T(session, 'order_reply_table_number')}_`;
            await sendText(sock, from, msg);
        } else {
            await sendText(sock, from, `🪑 ${T(session, 'order_which_table')}\n\n${T(session, 'enter_table')}`);
        }
    } catch (e) {
        console.error('getRestaurantTables error:', e);
        await sendText(sock, from, `🪑 ${T(session, 'order_which_table')}\n\n${T(session, 'enter_table')}`);
    }
}

async function handlePickTableForOrderState(sock, from, session, text) {
    if (text === 'home' || text === '0') {
        session.state = 'HOME';
        delete session.pending_order_text;
        delete session.pending_table_for;
        delete session.order_tables;
        await showHomeScreen(sock, from, session);
        return;
    }
    let tableNumber = null;
    let tableId = null;
    const tables = session.order_tables || [];
    if (session.menu_options && session.menu_options[text]) {
        const tableKey = session.menu_options[text];
        if (tableKey.startsWith('table_')) {
            tableId = tableKey.replace('table_', '');
            const t = tables.find(tbl => String(tbl.id) === String(tableId));
            if (t) {
                tableNumber = t.name;
                tableId = String(t.id);
            }
        }
    } else if (tables.length > 0) {
        const t = tables.find(tbl => String(tbl.name) === String(text) || String(tbl.id) === String(text));
        if (t) {
            tableNumber = t.name;
            tableId = String(t.id);
        }
    }
    if (!tableNumber && !tableId) {
        // Accept any table number as free text (e.g. "5") so order goes with that table even if not in list
        const trimmed = String(text).trim();
        if (trimmed.length > 0 && trimmed.length <= 20) {
            session.table_number = trimmed;
            session.table_id = null;
        } else {
            await sendText(sock, from, T(session, 'valid_table'));
            return;
        }
    } else {
        session.table_number = tableNumber || tableId;
        session.table_id = tableId ? parseInt(tableId, 10) : null;
    }
    const forWhat = session.pending_table_for;
    delete session.pending_table_for;
    delete session.order_tables;

    if (forWhat === 'text_order' && session.pending_order_text) {
        const orderText = session.pending_order_text;
        delete session.pending_order_text;
        await sendText(sock, from, `🔄 ${T(session, 'processing_order')}`);
        try {
            const result = await api.createOrderText({
                restaurant_id: session.restaurant_id,
                table_id: session.table_id,
                table_number: session.table_number,
                waiter_id: session.waiter_id,
                customer_name: session.customer_name,
                customer_phone: from.split('@')[0],
                order_text: orderText
            });
            if (result.success && result.order) {
                session.active_order_id = result.order.id;
                session.order_total = result.order.total;
                session.cart = [];
                let msg = `✅ *${T(session, 'order_received')}*\n`;
                msg += `🧾 ${T(session, 'order_id')}${result.order.id}\n`;
                msg += `🛒 *${T(session, 'items_found')}*\n`;
                if (result.order.items && result.order.items.length > 0) {
                    result.order.items.forEach(item => {
                        msg += `• ${item.name} x${item.quantity} = ${item.total?.toLocaleString()}/=\n`;
                    });
                }
                msg += `\n💰 *${T(session, 'total')} ${result.order.total?.toLocaleString()}/=*`;
                msg += `\n\n${T(session, 'waiter_confirm')}`;
                await sendButtons(sock, from, msg, [
                    { id: 'go_payment', text: `💳 ${T(session, 'pay_now')}` },
                    { id: 'track_order', text: `📍 ${T(session, 'track_status')}` },
                    { id: 'home', text: `🏠 ${T(session, 'home')}` }
                ], '🧾✨');
                session.state = 'HOME';
            } else {
                await sendText(sock, from, result.message || T(session, 'error_order'));
                session.state = 'MENU_IMAGE_ORDER';
                await showMenuImage(sock, from, session);
            }
        } catch (e) {
            console.error('Create order (text) error:', e);
            await sendText(sock, from, '❌ ' + T(session, 'error_try_again'));
            session.state = 'MENU_IMAGE_ORDER';
            await showMenuImage(sock, from, session);
        }
        return;
    }

    if (forWhat === 'cart') {
        await createOrder(sock, from, session);
    }
}

async function handleCallWaiterAskTableState(sock, from, session, text) {
    if (text === 'home') {
        session.state = 'HOME';
        await showHomeScreen(sock, from, session);
        return;
    }
    let tableNumber = null;
    let tableId = null;
    if (session.menu_options && session.menu_options[text]) {
        const tableKey = session.menu_options[text];
        if (tableKey.startsWith('table_')) {
            tableId = tableKey.replace('table_', '');
            const t = (session.call_waiter_tables || []).find(tbl => String(tbl.id) === String(tableId));
            if (t) {
                tableNumber = t.name;
            }
        }
    } else if (session.call_waiter_tables && session.call_waiter_tables.length > 0) {
        const t = session.call_waiter_tables.find(tbl => String(tbl.name) === String(text) || String(tbl.id) === String(text));
        if (t) {
            tableNumber = t.name;
            tableId = String(t.id);
        }
    }
    if (tableNumber || tableId) {
        if (tableNumber) session.table_number = tableNumber;
        if (tableId) session.table_id = tableId;
        const apiType = session.pending_call_type || 'call_waiter';
        const label = session.pending_call_label || T(session, 'call_stylist_label');
        session.state = 'HOME';
        await initiateCallWaiter(sock, from, session, apiType, label);
    } else {
        // Accept any seat label as free text even if not in manager list
        const trimmed = String(text).trim();
        if (trimmed.length > 0 && trimmed.length <= 20) {
            session.table_number = trimmed;
            session.table_id = null;
            const apiType = session.pending_call_type || 'call_waiter';
            const label = session.pending_call_label || T(session, 'call_stylist_label');
            session.state = 'HOME';
            await initiateCallWaiter(sock, from, session, apiType, label);
        } else {
            await sendText(sock, from, `❌ ${T(session, 'call_waiter_table_invalid')}`);
            await showCallWaiterAskTable(sock, from, session);
        }
    }
}

async function initiateCallWaiter(sock, from, session, apiType, displayName) {
    try {
        // If customer has a specific waiter (from QR scan), check if they're online first
        if (session.waiter_id) {
            let statusRes;
            try {
                statusRes = await api.getWaiterStatus(session.waiter_id);
            } catch (e) {
                console.error('Waiter status check error:', e);
            }
            if (statusRes && statusRes.success && statusRes.data && !statusRes.data.is_online) {
                const waiterName = statusRes.data.name || session.waiter_name || T(session, 'stylist_label');
                const msg = T(session, 'waiter_offline_msg').replace(/{name}/g, waiterName);
                await sendText(sock, from, `⚠️ ${msg}`);
                session.state = 'HOME';
                return;
            }
        }

        const payload = {
            restaurant_id: session.restaurant_id,
            table_number: session.table_number || '',
            waiter_id: session.waiter_id,
            request_type: apiType
        };
        if (session.table_id) payload.table_id = session.table_id;
        await api.callWaiter(payload);

        const sentMsg = T(session, 'call_waiter_sent').replace(/{label}/g, displayName);
        await sendText(sock, from, `✅ ${sentMsg}`);
        await showHomeScreen(sock, from, session);
    } catch (e) {
        console.error('Call waiter error:', e);
        await sendText(sock, from, `❌ ${T(session, 'call_waiter_failed')}`);
        await showHomeScreen(sock, from, session);
    }
}

async function handleMenuHubState(sock, from, session, text) {
    const t = text.toLowerCase();
    if (text.startsWith('cat_')) {
        const categoryId = text.replace('cat_', '');
        session.current_category = categoryId;
        await showItemsList(sock, from, session, categoryId);
    } else if (t.includes('chakula') || t.includes('vinywaji') || t.includes('drink') || t.includes('zaidi')) {
        await showMenuHub(sock, from, session);
    } else if (t === 'home' || t.includes('home') || t.includes('nyuma')) {
        await showHomeScreen(sock, from, session);
    } else {
        await showMenuHub(sock, from, session);
    }
}

async function showCategoriesList(sock, from, session, type) {
    session.state = 'CATEGORIES';

    try {
        if (!session.menu_cache) {
            const result = await api.getFullMenu(session.restaurant_id);
            if (result.success) {
                session.menu_cache = result.data;
            }
        }

        if (session.menu_cache && session.menu_cache.length > 0) {
            const rows = session.menu_cache.map(c => ({
                id: `cat_${c.id}`,
                title: `${c.name} (${c.menu_items?.length || 0})`,
                description: `${c.menu_items?.length || 0} ${T(session, 'categories_empty_suffix')}`
            }));

            rows.push({ id: 'home', title: '🏠 Home', description: '' });

            await sendList(sock, from,
                `📂 *${T(session, 'categories_select')}*`,
                T(session, 'categories_view'),
                [{ title: T(session, 'categories_view'), rows }]
            );
        } else {
            await sendText(sock, from, T(session, 'menu_unavailable'));
            await showHomeScreen(sock, from, session);
        }
    } catch (e) {
        console.error('Fetch categories error:', e);
        await showHomeScreen(sock, from, session);
    }
}

async function handleCategoriesState(sock, from, session, text) {
    if (text.startsWith('cat_')) {
        const categoryId = text.replace('cat_', '');
        session.current_category = categoryId;
        await showItemsList(sock, from, session, categoryId);
    } else if (text === 'back_menu') {
        await showMenuHub(sock, from, session);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    }
}

async function handleItemsListState(sock, from, session, text) {
    if (text.startsWith('item_')) {
        const itemId = text.replace('item_', '');
        await showItemDetail(sock, from, session, itemId);
    } else if (text === 'back_categories') {
        await showCategoriesList(sock, from, session, session.current_category_type);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    }
}

async function handleItemDetailState(sock, from, session, text) {
    if (text.startsWith('add_')) {
        const itemId = text.replace('add_', '');
        session.pending_item = itemId;
        session.pending_qty = 1;
        await showQuantitySelection(sock, from, session, itemId);
        session.state = 'QUANTITY';
    } else if (text === 'back_items') {
        await showItemsList(sock, from, session, session.current_category);
    } else if (text === 'go_cart') {
        await showCart(sock, from, session);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    }
}

async function handleQuantityState(sock, from, session, text) {
    if (text.startsWith('qty_')) {
        const parts = text.split('_');
        if (parts[1] === 'plus') {
            session.pending_qty++;
            await showQuantityMore(sock, from, session);
        } else if (parts[1] === 'minus') {
            session.pending_qty = Math.max(1, session.pending_qty - 1);
            await showQuantityMore(sock, from, session);
        } else if (parts[1] === 'done') {
            await addToCart(sock, from, session, session.pending_item, session.pending_qty);
        } else {
            const qty = parseInt(parts[1]);
            if (qty <= 3) {
                await addToCart(sock, from, session, session.pending_item, qty);
            } else {
                session.pending_qty = 3;
                session.state = 'QUANTITY_MORE';
                await showQuantityMore(sock, from, session);
            }
        }
    } else if (text === 'qty_more') {
        session.pending_qty = 3;
        session.state = 'QUANTITY_MORE';
        await showQuantityMore(sock, from, session);
    }
}

async function handleCartState(sock, from, session, text) {
    switch (text) {
        case 'confirm_order':
            await showConfirmOrder(sock, from, session);
            break;
        case 'edit_cart':
            await showCartEdit(sock, from, session);
            break;
        case 'clear_cart':
            session.cart = [];
            await sendText(sock, from, `🗑️ ${T(session, 'cart_cleared')}`);
            await showHomeScreen(sock, from, session);
            break;
        case 'home':
            await showHomeScreen(sock, from, session);
            break;
        case 'continue_menu':
            await showMenuHub(sock, from, session);
            break;
    }
}

async function handleCartEditState(sock, from, session, text) {
    if (text.startsWith('remove_')) {
        const idx = parseInt(text.replace('remove_', ''));
        if (session.cart[idx]) {
            const removed = session.cart.splice(idx, 1)[0];
            await sendText(sock, from, `❌ ${removed.name} removed.`);
        }
        await showCart(sock, from, session);
    } else if (text === 'back_cart') {
        await showCart(sock, from, session);
    }
}

async function handleConfirmOrderState(sock, from, session, text) {
    switch (text) {
        case 'confirm_yes':
            if (!session.table_number && !session.table_id) {
                session.pending_table_for = 'cart';
                await showOrderTableSelect(sock, from, session);
            } else {
                await createOrder(sock, from, session);
            }
            break;
        case 'back_cart':
            await showCart(sock, from, session);
            break;
        case 'cancel_order':
            session.cart = [];
            await sendText(sock, from, `❌ ${T(session, 'order_cancelled')}`);
            await showHomeScreen(sock, from, session);
            break;
    }
}

async function handlePaymentSummaryState(sock, from, session, text) {
    switch (text) {
        case 'pay_cash':
            await showCashPayment(sock, from, session);
            break;
        case 'pay_mobile':
            session.state = 'USSD_NUMBER';
            await sendText(sock, from, `📱 ${T(session, 'enter_phone_billed')}`);
            break;
        case 'home':
            await showHomeScreen(sock, from, session);
            break;
    }
}

async function handleCashPaymentState(sock, from, session, text) {
    switch (text) {
        case 'cash_paid':
            await sendText(sock, from, T(session, 'cash_thanks'));
            await showPostPaymentOptions(sock, from, session);
            break;
        case 'track_order':
            await showTrackStatus(sock, from, session);
            break;
        case 'home':
            await showHomeScreen(sock, from, session);
            break;
    }
}

async function handleProviderSelectState(sock, from, session, text) {
    if (text.startsWith('provider_')) {
        session.ussd_provider = text.replace('provider_', '');
        session.state = 'USSD_NUMBER';
        await sendText(sock, from, `📱 ${T(session, 'enter_phone_billed')}`);
    } else if (text === 'back_payment') {
        await showPaymentSummary(sock, from, session);
    }
}

async function handleUssdNumberState(sock, from, session, text) {
    // Validate phone number
    if (/^(0\d{9}|255\d{9})$/.test(text)) {
        session.ussd_phone = text.startsWith('255') ? '0' + text.slice(3) : text;
        session.ussd_provider = detectNetwork(session.ussd_phone);
        await showPayNow(sock, from, session);
    } else {
        await sendText(sock, from, `❌ ${T(session, 'invalid_phone_format')}`);
    }
}

async function handlePayNowState(sock, from, session, text) {
    switch (text) {
        case 'paynow':
            await initiateUssdPayment(sock, from, session);
            break;
        case 'change_number':
            session.state = 'USSD_NUMBER';
            await sendText(sock, from, 'Andika namba mpya ya simu:');
            break;
        case 'back_provider':
            await showProviderSelect(sock, from, session);
            break;
    }
}

async function handleUssdPendingState(sock, from, session, text) {
    switch (text) {
        case 'check_status':
            await checkPaymentStatus(sock, from, session);
            break;
        case 'cancel_payment':
            await showPaymentSummary(sock, from, session);
            break;
        case 'manual_ussd':
            await showManualUssd(sock, from, session);
            break;
        case 'home':
            await showHomeScreen(sock, from, session);
            break;
    }
}

async function handleManualUssdState(sock, from, session, text) {
    if (text === 'manual_paid') {
        session.state = 'USSD_PENDING';
        await sendText(sock, from, T(session, 'txn_id_prompt'));
    } else if (text === 'pay_cash') {
        await showCashPayment(sock, from, session);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    } else {
        // Assume it's a transaction ID
        session.transaction_id = text;
        await sendText(sock, from, T(session, 'txn_received'));
        await showPostPaymentOptions(sock, from, session);
    }
}

async function handleTrackStatusState(sock, from, session, text) {
    switch (text) {
        case 'refresh':
            await showTrackStatus(sock, from, session);
            break;
        case 'go_payment':
            await showPaymentSummary(sock, from, session);
            break;
        case 'rate_service':
            await showFeedbackTypeSelection(sock, from, session);
            break;
        case 'home':
            await showHomeScreen(sock, from, session);
            break;
    }
}

async function handleFeedbackState(sock, from, session, text) {
    if (session.state === 'FEEDBACK_TYPE') {
        if (text === 'rate_restaurant') {
            session.feedback_waiter_id = null;
            session.feedback_waiter_name = null;
            await showFeedbackA(sock, from, session);
        } else if (text === 'rate_waiter') {
            if (session.waiter_id && session.waiter_name) {
                // Auto-select assigned waiter
                session.feedback_waiter_id = session.waiter_id;
                session.feedback_waiter_name = session.waiter_name;
                await showFeedbackA(sock, from, session);
            } else {
                await showWaiterFeedbackList(sock, from, session);
            }
        } else if (text === 'home') {
            await showHomeScreen(sock, from, session);
        }
    } else if (session.state === 'FEEDBACK_WAITER_LIST') {
        if (text.startsWith('rate_waiter_')) {
            const parts = text.replace('rate_waiter_', '').split('|');
            session.feedback_waiter_id = parts[0];
            session.feedback_waiter_name = parts[1];
            await showFeedbackA(sock, from, session);
        } else if (text === 'home') {
            await showHomeScreen(sock, from, session);
        }
    } else if (text.startsWith('rate_')) {
        const rating = text.replace('rate_', '');
        session.rating = parseInt(rating);
        session.state = 'FEEDBACK_COMMENT';

        await sendText(sock, from, `📝 ${T(session, 'comment_prompt')}`);
    }
}

async function handleFeedbackCommentState(sock, from, session, text) {
    const comment = (text.toLowerCase() === 'end' || text.toLowerCase() === 'skip') ? '' : text;

    try {
        await api.submitFeedback({
            restaurant_id: session.restaurant_id,
            customer_phone: from.split('@')[0],
            rating: session.rating,
            comment: comment,
            waiter_id: session.feedback_waiter_id
        });
    } catch (e) {
        console.error('Feedback error:', e);
    }

    await sendText(sock, from, `🙏 ${T(session, 'thanks_feedback')}`);
    await showHomeScreen(sock, from, session);
}

async function handleTipState(sock, from, session, text) {
    if (text.startsWith('tip_')) {
        const amount = text.replace('tip_', '');
        if (amount !== 'skip') {
            if (!session.active_order_id) {
                await sendText(sock, from, `⚠️ ${T(session, 'tip_no_booking')}`);
            } else {
                try {
                    await api.submitTip({
                        restaurant_id: session.restaurant_id,
                        order_id: session.active_order_id,
                        amount: parseInt(amount)
                    });
                    await sendText(sock, from, `💝 ${TRepl(session, 'tip_thanks_amount', { amount })}`);
                } catch (e) {
                    console.error('Tip error:', e);
                    await sendText(sock, from, `❌ ${T(session, 'tip_error')}`);
                }
            }
        }

        await sendText(sock, from, `🎉 ${T(session, 'thanks_using_tiptap')}`);
        await showHomeScreen(sock, from, session);
    }
}

// ═══════════════════════════════════════════════════════════════
// SCREEN BUILDERS
// ═══════════════════════════════════════════════════════════════

async function showHomeScreen(sock, from, session) {
    session.state = 'HOME';
    // Clear temporary payment/tip info
    delete session.tip_waiter_id;
    delete session.tip_waiter_name;
    delete session.feedback_waiter_id;
    delete session.feedback_waiter_name;
    session.quick_payment_desc = null;
    session.quick_payment_mode = null;

    const name = session.restaurant_name || T(session, 'saloon_fallback_name');
    const info = session.header_info || session.waiter_name || (session.table_number ? `${T(session, 'table')} ${session.table_number}` : '-');

    const rows = [
        { id: 'view_menu', title: `🍽️ ${T(session, 'menu_view')}`, description: T(session, 'menu_view_desc') },
        { id: 'rate_service', title: session.waiter_name ? `⭐ ${T(session, 'rate_service')} ${session.waiter_name.toUpperCase()}` : `⭐ ${T(session, 'rate_service')}`, description: T(session, 'rate_desc') },
        { id: 'live_bill', title: `💳 ${T(session, 'pay_bill')}`, description: T(session, 'pay_bill_desc') },
        { id: 'give_tips', title: session.waiter_name ? `💵 ${T(session, 'tip')} ${session.waiter_name.toUpperCase()}` : `💵 ${T(session, 'tip')}`, description: T(session, 'tip_desc') }
    ];

    if (session.waiter_id) {
        rows.push({ id: 'call_waiter', title: `🔔 ${T(session, 'call_waiter')}`, description: T(session, 'call_waiter_desc') });
    }

    rows.push({ id: 'guest_wifi', title: `📶 ${T(session, 'guest_wifi')}`, description: T(session, 'guest_wifi_desc') });

    rows.push({ id: 'change_language', title: `🌐 ${T(session, 'change_language')}`, description: T(session, 'change_language_desc') });
    rows.push({ id: 'exit_bot', title: `❌ ${T(session, 'exit')}`, description: T(session, 'exit_desc') });

    await sendList(sock, from,
        `👋 ${T(session, 'home_welcome')} *${name}* (${info})\n${T(session, 'home_choose')}\n_${T(session, 'home_type_zero')}_`,
        T(session, 'serviceDesk'),
        [
            {
                title: `🍽️ ${T(session, 'home_main_services')}`,
                rows: rows
            }
        ],
        '🏠✨'
    );
}

async function showLanguageSelect(sock, from, session) {
    session.state = 'LANGUAGE_SELECT';
    await sendButtons(sock, from, T(session, 'select_language'), [
        { id: 'lang_en', text: `🇬🇧 ${T(session, 'lang_english')}` },
        { id: 'lang_sw', text: `🇹🇿 ${T(session, 'lang_swahili')}` }
    ], '🌐');
}

async function handleLanguageSelectState(sock, from, session, text) {
    if (text === 'lang_en') {
        session.lang = 'en';
        await sendText(sock, from, T(session, 'language_changed'));
        await showHomeScreen(sock, from, session);
    } else if (text === 'lang_sw') {
        session.lang = 'sw';
        await sendText(sock, from, T(session, 'language_changed_sw'));
        await showHomeScreen(sock, from, session);
    } else {
        await showLanguageSelect(sock, from, session);
    }
}

async function showTableSelection(sock, from, session) {
    try {
        const result = await api.getRestaurantTables(session.restaurant_id);
        if (result.success && result.data.length > 0) {
            let text = `━━━━━━━━ 🪑 ━━━━━━━━\n`;
            text += `🧾 *${T(session, 'pick_seat_title')}*\n`;

            session.menu_options = {};
            result.data.slice(0, 10).forEach((t, i) => {
                const numEmoji = getNumberEmoji(i + 1);
                const cap = t.capacity ?? '—';
                text += `${numEmoji} ${TRepl(session, 'pick_seat_line', { name: t.name, cap })}\n`;
                session.menu_options[(i + 1).toString()] = `table_${t.id}`;
            });

            text += `${T(session, 'choose_number_hint')}\n`;
            text += `━━━━━━━━ ✨ ━━━━━━━━`;
            await sendText(sock, from, text);
        } else {
            await sendText(sock, from, T(session, 'pick_seat_free'));
        }
    } catch (e) {
        console.error('Fetch tables error:', e);
        await sendText(sock, from, T(session, 'pick_seat_free'));
    }
}

async function showMenuHub(sock, from, session) {
    session.state = 'MENU_HUB';

    try {
        // Use the bot-specific full-menu endpoint instead of manager categories
        if (!session.menu_cache) {
            const result = await api.getFullMenu(session.restaurant_id);
            if (result.success) {
                session.menu_cache = result.data;
            }
        }

        if (session.menu_cache && session.menu_cache.length > 0) {
            const rows = session.menu_cache.map(c => ({
                id: `cat_${c.id}`,
                title: `📂${c.name.replace(/\s/g, '')}`
            }));

            const sections = [
                {
                    title: `🔍${T(session, 'section_search')}`,
                    rows: [{ id: 'search_food', title: `🔎${T(session, 'search_services_hint')}` }]
                },
                {
                    title: `📋${T(session, 'section_categories')}`,
                    rows: rows
                },
                {
                    title: `🏠${T(session, 'section_home')}`,
                    rows: [{ id: 'home', title: `🔙${T(session, 'home')}` }]
                }
            ];

            await sendList(sock, from, `💇 *${T(session, 'service_catalog_header')}*`, T(session, 'menu_pick_list'), sections, '💇✨');
        } else {
            await sendText(sock, from, T(session, 'menu_unavailable'));
            await showHomeScreen(sock, from, session);
        }
    } catch (e) {
        console.error('Fetch menu error:', e);
        await sendText(sock, from, T(session, 'menu_fetch_error'));
    }
}

async function showItemsList(sock, from, session, categoryId) {
    session.state = 'ITEMS_LIST';
    session.current_category = categoryId;

    const category = (session.menu_cache || []).find(c => c.id == categoryId);

    if (category && category.menu_items && category.menu_items.length > 0) {
        if (!session.menu_items_cache) session.menu_items_cache = [];
        category.menu_items.forEach(item => {
            if (!session.menu_items_cache.find(i => i.id == item.id)) {
                session.menu_items_cache.push(item);
            }
        });

        const rows = category.menu_items.map(i => ({
            id: `item_${i.id}`,
            title: `🍲${i.name.replace(/\s/g, '')} - ${i.price.toLocaleString()}/=`,
            description: `${i.price.toLocaleString()}/=`
        }));

        await sendList(sock, from, `💇${category.name.toUpperCase().replace(/\s/g, '')}`, T(session, 'services'), [
            {
                title: '📋LIST',
                rows: rows
            },
            {
                title: '🏠HOME',
                rows: [
                    { id: 'back_menu', title: '🔙BackMenu' },
                    { id: 'go_cart', title: '🛒MyOrder' }
                ]
            }
        ], '✨🍴');
    } else {
        await sendText(sock, from, T(session, 'items_none'));
        await showMenuHub(sock, from, session);
    }
}

async function showItemDetail(sock, from, session, itemId) {
    session.state = 'ITEM_DETAIL';
    session.pending_item = itemId;

    const item = (session.menu_items_cache || []).find(i => i.id == itemId);

    if (!item) {
        await sendText(sock, from, T(session, 'item_not_found'));
        return await showMenuHub(sock, from, session);
    }

    const text =
        `🍲*${item.name.replace(/\s/g, '')}*\n` +
        `💰${item.price?.toLocaleString()}/=\n` +
        `${item.description ? `📝${item.description}\n` : ''}`;

    const buttons = [
        { id: `add_${itemId}`, text: '➕Add' },
        { id: 'back_items', text: '🔙Back' },
        { id: 'go_cart', text: '🛒Order' }
    ];

    if (item.image) {
        await sendImageWithButtons(sock, from, item.image, text, buttons, '🍲✨');
    } else {
        await sendButtons(sock, from, text, buttons, '🍲✨');
    }
}

async function showQuantitySelection(sock, from, session, itemId) {
    await sendList(sock, from,
        '🔢*Quantity?*',
        'Choose',
        [
            {
                title: '⚡CHOOSE',
                rows: [
                    { id: 'qty_1', title: '1' },
                    { id: 'qty_2', title: '2' },
                    { id: 'qty_3', title: '3' },
                    { id: 'qty_4', title: '4' },
                    { id: 'qty_5', title: '5' }
                ]
            },
            {
                title: '🏠HOME',
                rows: [
                    { id: 'qty_more', title: '🔢OtherNumber' }
                ]
            }
        ],
        '🔢✨'
    );
}

async function showQuantityMore(sock, from, session) {
    await sendButtons(sock, from,
        `🔢Quantity: *${session.pending_qty}*`,
        [
            { id: 'qty_plus', text: '➕+1' },
            { id: 'qty_minus', text: '➖-1' },
            { id: 'qty_done', text: '✅Done' }
        ]
    );
}

async function addToCart(sock, from, session, itemId, qty) {
    let item = (session.menu_items_cache || []).find(i => i.id == itemId);
    if (!item) return;

    const existing = session.cart.find(c => c.menu_id == itemId);
    if (existing) {
        existing.qty += qty;
    } else {
        session.cart.push({
            menu_id: itemId,
            name: item.name,
            price: item.price,
            qty: qty
        });
    }

    const total = session.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);

    await sendButtons(sock, from,
        `✅*${T(session, 'added_title')}!*\n` +
        `${item.name} x${qty}\n` +
        `${T(session, 'total')} ${total.toLocaleString()}/=`,
        [
            { id: 'continue_menu', text: `➕ ${T(session, 'continue_shopping')}` },
            { id: 'go_cart', text: `🛒 ${T(session, 'go_cart')}` },
            { id: 'home', text: `🏠 ${T(session, 'home')}` }
        ]
    );
    session.state = 'CART';
}

async function showCart(sock, from, session) {
    session.state = 'CART';

    if (session.cart.length === 0) {
        await sendButtons(sock, from,
            `🛒*${T(session, 'cart_empty_title')}*`,
            [
                { id: 'go_menu', text: `💇 ${T(session, 'menu_view')}` },
                { id: 'home', text: `🏠 ${T(session, 'home')}` }
            ]
        );
        return;
    }

    let text = `🛒*${T(session, 'cart_title')}*\n`;
    let total = 0;
    session.cart.forEach((item, i) => {
        const subtotal = item.price * item.qty;
        text += `${i + 1}.${item.name} x${item.qty}=${subtotal.toLocaleString()}/=\n`;
        total += subtotal;
    });
    text += `💰*Total: ${total.toLocaleString()}/=*`;
    session.order_total = total;

    await sendList(sock, from, text, 'Choose', [
        {
            title: '⚡ACTIONS',
            rows: [
                { id: 'confirm_order', title: '✅Confirm' },
                { id: 'continue_menu', title: '➕AddMore' },
                { id: 'edit_cart', title: '✏️Edit' }
            ]
        },
        {
            title: '🏠HOME',
            rows: [
                { id: 'home', title: '🔙BackHome' }
            ]
        }
    ], '🛒✨');
}

async function showCartEdit(sock, from, session) {
    session.state = 'CART_EDIT';
    const rows = session.cart.map((item, i) => ({
        id: `remove_${i}`,
        title: `❌${item.name.replace(/\s/g, '')} (x${item.qty})`,
        description: `x${item.qty}`
    }));
    rows.push({ id: 'back_cart', title: '🔙BackCart' });

    await sendList(sock, from, `✏️*${T(session, 'edit_cart_title')}*`, T(session, 'view_lines'), [{ title: T(session, 'view_lines'), rows }], '✏️✨');
}

async function showConfirmOrder(sock, from, session) {
    session.state = 'CONFIRM_ORDER';
    let text = `🧾*${T(session, 'confirm_booking_title')}*\n`;
    text += `📍${T(session, 'table')}: ${session.table_number}\n`;
    session.cart.forEach(item => { text += `•${item.name} x${item.qty}\n`; });
    text += `💰*Total:${session.order_total.toLocaleString()}/=*`;

    await sendButtons(sock, from, text, [
        { id: 'confirm_yes', text: '✅Confirm' },
        { id: 'back_cart', text: '🔙Back' },
        { id: 'cancel_order', text: '❌Cancel' }
    ], '🧾✨');
}

async function createOrder(sock, from, session) {
    try {
        const result = await api.createOrder({
            restaurant_id: session.restaurant_id,
            table_id: session.table_id,
            table_number: session.table_number,
            customer_phone: from.split('@')[0],
            customer_name: session.customer_name,
            items: session.cart,
            waiter_id: session.waiter_id
        });

        if (result.success) {
            session.active_order_id = result.order_id;
            session.order_total = result.total;
            session.cart = [];

            await sendButtons(sock, from,
                `✅*${T(session, 'order_received')}*\n` +
                `🧾${T(session, 'order_id')}${result.order_id}\n` +
                `💰${result.total.toLocaleString()}/=\n` +
                `${T(session, 'waiter_confirm')}`,
                [
                    { id: 'go_payment', text: `💳 ${T(session, 'pay_now')}` },
                    { id: 'track_order', text: `📍 ${T(session, 'track_status')}` },
                    { id: 'home', text: `🏠 ${T(session, 'home')}` }
                ]
            );
            session.state = 'HOME';
        }
    } catch (error) {
        console.error('Create order error:', error);
        await sendText(sock, from, `❌ ${T(session, 'error_order')}`);
    }
}

async function showPaymentSummary(sock, from, session) {
    session.state = 'PAYMENT_SUMMARY';
    if (!session.active_order_id) {
        await sendText(sock, from, T(session, 'no_booking_pay'));
        return await showHomeScreen(sock, from, session);
    }

    let text = `🧾*${T(session, 'bill_header')}*\n`;
    text += `📋${T(session, 'order_id')}${session.active_order_id}\n`;
    text += `💰*Total:${session.order_total?.toLocaleString() || 0}/=*\n`;

    await sendList(sock, from, text, T(session, 'pay_bill'), [
        {
            title: '💳PAYMENT',
            rows: [
                { id: 'pay_mobile', title: '📲MobileMoney' },
                { id: 'pay_cash', title: '💵Cash' }
            ]
        },
        {
            title: '🏠HOME',
            rows: [
                { id: 'home', title: '🔙BackHome' }
            ]
        }
    ], '💳✨');
}

async function showCashPayment(sock, from, session) {
    session.state = 'CASH_PAYMENT';
    await sendButtons(sock, from,
        `💵*${T(session, 'pay_bill')} — ${T(session, 'pay_bill_desc')}*\n` +
        `${T(session, 'cash_pay_instructions')}`,
        [
            { id: 'cash_paid', text: `✅ ${T(session, 'cash_paid_btn')}` },
            { id: 'track_order', text: `📍 ${T(session, 'track_status')}` },
            { id: 'home', text: `🏠 ${T(session, 'home')}` }
        ]
    );
}

async function showProviderSelect(sock, from, session) {
    session.state = 'PROVIDER_SELECT';
    const rows = [
        { id: 'provider_mpesa', title: 'M-Pesa' },
        { id: 'provider_tigopesa', title: 'TigoPesa' },
        { id: 'provider_airtelmoney', title: 'AirtelMoney' },
        { id: 'provider_halopesa', title: 'HaloPesa' },
        { id: 'back_payment', title: '🔙Back' }
    ];
    await sendList(sock, from, '📲*MobileMoney*', 'Choose', [{ title: 'Networks', rows }], '📲✨');
}

async function showPayNow(sock, from, session) {
    session.state = 'PAY_NOW';
    await sendButtons(sock, from,
        `📲*Pay Now*\n` +
        `💰${session.order_total?.toLocaleString() || 0}/=\n` +
        `📱${session.ussd_phone}\n` +
        `Press "PAY NOW".`,
        [
            { id: 'paynow', text: '✅PAY NOW' },
            { id: 'change_number', text: '✍️Edit' },
            { id: 'back_provider', text: '⬅️Back' }
        ]
    );
}

async function initiateUssdPayment(sock, from, session) {
    try {
        const result = await api.initiateUssdPayment({
            order_id: session.active_order_id,
            phone: session.ussd_phone,
            amount: session.order_total,
            network: session.ussd_provider
        });
        if (result.success) {
            session.state = 'USSD_PENDING';
            await sendButtons(sock, from,
                '📲 *Request Sent!*\n' +
                'Confirm on your phone.\n\n' +
                `✅ *${T(session, 'bot_confirm_auto')}*`,
                [
                    { id: 'manual_ussd', text: '📟 Manual' },
                    { id: 'home', text: '🏠 Home' }
                ]
            );
            startPaymentPolling(sock, from, session, 'order', session.active_order_id);
        }
    } catch (error) {
        console.error('USSD error:', error);
        await sendButtons(sock, from, `❌ ${T(session, 'ussd_error')}`, [
            { id: 'paynow', text: `🔁 ${T(session, 'try_again')}` },
            { id: 'pay_cash', text: `💵 ${T(session, 'cash_option')}` }
        ]);
    }
}

async function checkPaymentStatus(sock, from, session) {
    try {
        const result = await api.getOrderStatus(session.active_order_id);
        if (result.payment_status === 'paid') {
            await sendButtons(sock, from, T(session, 'payment_confirmed_thanks'), [
                { id: 'track_order', text: `📍 ${T(session, 'track_booking')}` },
                { id: 'rate_service', text: `⭐ ${T(session, 'rate_service_btn')}` },
                { id: 'home', text: `🏠 ${T(session, 'home')}` }
            ]);
            session.state = 'HOME';
        } else {
            const status = result.status || 'Pending';
            const payStatus = result.payment_status || 'Pending';
            await sendButtons(sock, from,
                `⏳ *${T(session, 'payment_status_title')}*\n\n` +
                TRepl(session, 'payment_status_line', { s: status, p: payStatus }),
                [
                    { id: 'check_status', text: `🔄 ${T(session, 'refresh_btn')}` },
                    { id: 'home', text: `🏠 ${T(session, 'home')}` }
                ]
            );
        }
    } catch (error) { console.error(error); }
}

async function showManualUssd(sock, from, session) {
    session.state = 'MANUAL_USSD';
    await sendButtons(sock, from,
        `📟*${T(session, 'manual_ussd_title')}*\n` +
        `${T(session, 'manual_ussd_dial')}\n` +
        `${T(session, 'manual_ussd_amount')}: ${session.order_total?.toLocaleString()}/=\n` +
        `${T(session, 'manual_ussd_when_done')}`,
        [
            { id: 'manual_paid', text: `✅ ${T(session, 'cash_paid_btn')}` },
            { id: 'home', text: `🏠 ${T(session, 'home')}` }
        ]
    );
}

async function showPostPaymentOptions(sock, from, session) {
    await sendButtons(sock, from, T(session, 'choose_next'), [
        { id: 'track_order', text: `📍 ${T(session, 'track_booking')}` },
        { id: 'rate_service', text: `⭐ ${T(session, 'rate_service_btn')}` },
        { id: 'home', text: `🏠 ${T(session, 'home')}` }
    ]);
}

async function showTrackStatus(sock, from, session) {
    session.state = 'TRACK_STATUS';
    try {
        const result = await api.getActiveOrder(session.restaurant_id, session.table_number);
        const order = result.order || (result.data ? {
            id: result.data.order_id,
            total: result.data.total,
            status: result.data.status,
            payment_status: result.data.payment_status ?? 'unpaid',
            waiter_name: result.data.waiter_name,
            items: result.data.items || [],
        } : null);

        if (!result.success || !order) {
            await sendText(sock, from, T(session, 'no_active_booking_seat'));
            return await showHomeScreen(sock, from, session);
        }

        session.active_order_id = order.id;

        const statusIcons = {
            pending: T(session, 'status_icons_pending'),
            confirmed: T(session, 'status_icons_confirmed'),
            preparing: T(session, 'status_icons_preparing'),
            ready: T(session, 'status_icons_ready'),
            served: T(session, 'status_icons_served'),
            paid: T(session, 'status_icons_paid'),
        };

        let text = `📍 *${T(session, 'order_id')}${order.id}*\n`;
        text += `${T(session, 'booking_status_label')}: ${statusIcons[order.status] || order.status}\n`;
        text += `${T(session, 'payment_label')}: ${order.payment_status === 'paid' ? T(session, 'status_payment_paid') : T(session, 'status_payment_pending')}\n`;
        if (order.waiter_name) {
            text += `🙋 ${T(session, 'stylist_label')}: ${order.waiter_name}\n`;
        }

        text += `\n🛒 *${T(session, 'service_lines')}*\n`;
        (order.items || []).forEach(item => {
            text += `• ${item.name} x${item.quantity}\n`;
        });

        text += `\n💰 *${T(session, 'total')} Tsh ${order.total?.toLocaleString()}/=*`;

        const buttons = [
            { id: 'refresh', text: `🔄 ${T(session, 'refresh_btn')}` }
        ];

        if (order.payment_status !== 'paid') {
            buttons.push({ id: 'go_payment', text: `💳 ${T(session, 'pay_now')}` });
        }

        if (order.status === 'served' || order.status === 'ready' || order.payment_status === 'paid') {
            buttons.push({ id: 'rate_service', text: `⭐ ${T(session, 'rate_service_btn')}` });
        }

        buttons.push({ id: 'home', text: `🏠 ${T(session, 'home')}` });

        await sendButtons(sock, from, text, buttons, '📡✨');
    } catch (e) {
        console.error('Track status error:', e);
        await sendText(sock, from, `❌ ${T(session, 'track_fetch_error')}`);
    }
}

async function showFeedbackTypeSelection(sock, from, session) {
    session.state = 'FEEDBACK_TYPE';
    await sendButtons(sock, from, `⭐ *${T(session, 'feedback_prompt_title')}*\n${T(session, 'feedback_prompt_body')}`, [
        { id: 'rate_restaurant', text: `🏢 ${T(session, 'feedback_saloon')}` },
        { id: 'rate_waiter', text: `🙋 ${T(session, 'feedback_stylist')}` },
        { id: 'home', text: `🏠 ${T(session, 'home')}` }
    ], '⭐✨');
}

async function showWaiterFeedbackList(sock, from, session) {
    session.state = 'FEEDBACK_WAITER_LIST';
    try {
        const result = await api.getWaiters(session.restaurant_id);
        if (result.success && result.data.length > 0) {
            const rows = result.data.map(w => ({
                id: `rate_waiter_${w.id}|${w.name}`,
                title: `🙋 ${w.name}`,
                description: T(session, 'feedback_row_hint')
            }));
            rows.push({ id: 'home', title: `🏠 ${T(session, 'home')}` });

            const title = session.lang === 'sw' ? T(session, 'feedback_pick_stylist_sw') : T(session, 'feedback_pick_stylist');
            await sendList(sock, from, `🙋 ${title}`, T(session, 'waiters_list_btn'), [{ title: T(session, 'waiters_list_title'), rows }], '🙋✨');
        } else {
            await sendText(sock, from, session.lang === 'sw' ? T(session, 'feedback_no_stylists_sw') : T(session, 'feedback_no_stylists'));
            await showHomeScreen(sock, from, session);
        }
    } catch (e) {
        console.error('Fetch waiters error:', e);
        await showHomeScreen(sock, from, session);
    }
}

async function showFeedbackA(sock, from, session) {
    session.state = 'FEEDBACK';
    const title = session.feedback_waiter_name
        ? `⭐ *${TRepl(session, 'feedback_rating_for', { name: session.feedback_waiter_name })}*`
        : `⭐ *${T(session, 'feedback_rate_title')}*`;
    await sendButtons(sock, from, `${title}\n${T(session, 'feedback_stars_intro')}`, [
        { id: 'rate_1', text: '⭐1' },
        { id: 'rate_2', text: '⭐⭐2' },
        { id: 'rate_3', text: '⭐⭐⭐3' },
        { id: 'rate_4', text: '⭐⭐⭐⭐4' },
        { id: 'rate_5', text: '⭐⭐⭐⭐⭐5' }
    ], '⭐✨');
}

async function showCallWaiterOptions(sock, from, session) {
    session.state = 'CALL_WAITER';
    await sendButtons(sock, from, T(session, 'call_options_prompt'), [
        { id: 'call_only', text: `🙋 ${T(session, 'call_stylist_label')}` },
        { id: 'request_bill', text: `🧾 ${T(session, 'request_bill_label')}` },
        { id: 'list_waiters', text: `👥 ${T(session, 'waiters_list_btn')}` },
        { id: 'home', text: `🏠 ${T(session, 'home')}` }
    ], '🙋✨');
}

async function showWaitersList(sock, from, session) {
    session.state = 'WAITERS_LIST';
    try {
        const result = await api.getWaiters(session.restaurant_id);
        if (result.success && result.data.length > 0) {
            const rows = result.data.map(w => ({
                id: `call_waiter_${w.id}|${w.name}`,
                title: w.is_online ? `🙋 ${w.name}` : `🙋 ${w.name} ${T(session, 'waiters_offline_badge')}`,
                description: w.is_online ? T(session, 'waiters_tap_to_call') : T(session, 'waiters_not_on_duty')
            }));

            rows.push({ id: 'home', title: `🏠 ${T(session, 'home')}`, description: '' });

            await sendList(sock, from,
                `👥 *${T(session, 'waiters_list_title')}*\n\n${T(session, 'waiters_list_subtitle')}`,
                T(session, 'waiters_list_btn'),
                [{ title: T(session, 'waiters_list_title'), rows }],
                '👥✨'
            );
        } else {
            await sendText(sock, from, T(session, 'waiters_list_empty'));
            await showCallWaiterOptions(sock, from, session);
        }
    } catch (e) {
        console.error('Fetch waiters error:', e);
        await showCallWaiterOptions(sock, from, session);
    }
}

async function showTipScreen(sock, from, session) {
    session.state = 'TIP';
    await sendButtons(sock, from, `💝*${T(session, 'tip')}*\n${T(session, 'tip_choose_amount')}`, [
        { id: 'tip_500', text: '500/=' },
        { id: 'tip_1000', text: '1,000/=' },
        { id: 'tip_skip', text: T(session, 'tip_skip') }
    ], '💝✨');
}

async function handleSearchRestaurant(sock, from, session, query) {
    try {
        const result = await api.searchRestaurant(query);
        if (result.success && result.data?.length > 0) {
            const restaurants = result.data.slice(0, 5);
            session.search_results = restaurants;
            session.menu_options = {};

            let text = `━━━━━━━━ 🔍 ━━━━━━━━\n`;
            text += `✅ ${T(session, 'found_saloons')} ${result.count}\n`;
            text += `👇 ${T(session, 'choose_number_hint')}\n`;

            restaurants.forEach((r, i) => {
                const numEmoji = getNumberEmoji(i + 1);
                text += `${numEmoji} 🏠 ${r.name}\n📍 ${r.location || 'Tanzania'}\n`;
                session.menu_options[(i + 1).toString()] = `pick_rest_${r.id}`;
            });

            text += `0️⃣ 🔄 Search again\n`;
            session.menu_options['0'] = 'search_again';

            text += `━━━━━━━━ ✨ ━━━━━━━━`;
            await sendText(sock, from, text);
            session.state = 'SEARCH_RESTAURANT';
        } else {
            await sendText(sock, from, T(session, 'search_not_found'));
        }
    } catch (e) { await sendText(sock, from, `❌ ${T(session, 'search_error')}`); }
}

async function showMenuSelection(sock, from, session) {
    session.state = 'MENU_SELECTION';
    await sendButtons(sock, from, T(session, 'menu_pick_title'), [
        { id: 'menu_image', text: `🖼️ ${T(session, 'menu_pick_image')}` },
        { id: 'menu_list', text: `📋 ${T(session, 'menu_pick_list')}` },
        { id: 'home', text: `🏠 ${T(session, 'home')}` }
    ], '💇✨');
}

async function handleMenuSelectionState(sock, from, session, text) {
    if (text === 'menu_image') {
        await showMenuImage(sock, from, session);
    } else if (text === 'menu_list') {
        await showMenuHub(sock, from, session);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    } else {
        await showMenuSelection(sock, from, session);
    }
}

async function showMenuImage(sock, from, session) {
    session.state = 'MENU_IMAGE_ORDER';
    await sendText(sock, from, `🔄 ${T(session, 'downloading_menu')}`);

    const result = await api.getMenuImage(session.restaurant_id);
    if (result.success && result.data.menu_image_url) {
        const cmdZero = T(session, 'menu_cmd_zero');
        const cmdOrder = T(session, 'menu_cmd_order');
        const caption = `👆 ${T(session, 'here_is_menu')}\n\n*${T(session, 'menu_commands')}*\n• ${cmdZero}\n• ${cmdOrder}`;
        try {
            await sock.sendMessage(from, { image: { url: result.data.menu_image_url }, caption: caption });
        } catch (e) {
            await sendText(sock, from, caption);
        }
        // No extra "Choose: 1 Home (0)" bubble — user types 0 to go back (see caption).
    } else {
        await sendText(sock, from, `❌ ${T(session, 'menu_not_available')}`);
        session.state = 'HOME';
        await showHomeScreen(sock, from, session);
    }
}

async function handleMenuImageOrderState(sock, from, session, text) {
    // 0 or home = back to main menu (0 is also handled globally)
    if (text === 'home' || text === '0') {
        await showHomeScreen(sock, from, session);
        return;
    }

    // If table not set (e.g. came via waiter QR), ask which table before submitting order
    if (!session.table_number && !session.table_id) {
        session.pending_order_text = text;
        session.pending_table_for = 'text_order';
        await showOrderTableSelect(sock, from, session);
        return;
    }

    await sendText(sock, from, `🔄 ${T(session, 'processing_order')}`);

    try {
        const result = await api.createOrderText({
            restaurant_id: session.restaurant_id,
            table_id: session.table_id,
            table_number: session.table_number,
            waiter_id: session.waiter_id,
            customer_name: session.customer_name,
            customer_phone: from.split('@')[0],
            order_text: text
        });

        if (result.success) {
            if (result.order) {
                session.active_order_id = result.order.id;
                session.order_total = result.order.total;
                session.cart = []; // Clear cart if any

                let msg = `✅ *${T(session, 'order_received')}*\n`;
                msg += `🧾 ${T(session, 'order_id')}${result.order.id}\n`;
                msg += `🛒 *${T(session, 'items_found')}*\n`;

                if (result.order.items && result.order.items.length > 0) {
                    result.order.items.forEach(item => {
                        msg += `• ${item.name} x${item.quantity} = ${item.total?.toLocaleString()}/=\n`;
                    });
                }

                msg += `\n💰 *${T(session, 'total')} ${result.order.total?.toLocaleString()}/=*`;
                msg += `\n\n${T(session, 'waiter_confirm')}`;

                await sendButtons(sock, from, msg, [
                    { id: 'go_payment', text: `💳 ${T(session, 'pay_now')}` },
                    { id: 'track_order', text: `📍 ${T(session, 'track_status')}` },
                    { id: 'home', text: `🏠 ${T(session, 'home')}` }
                ], '🧾✨');
            } else {
                // Handle success but no order object (e.g. just a message)
                await sendText(sock, from, result.message || T(session, 'order_received_generic'));
                await showHomeScreen(sock, from, session);
            }

            session.state = 'HOME';
        } else {
            await sendText(sock, from, `❌ ${result.message || T(session, 'error_order')}\n\n${T(session, 'try_clear')}`);
            await sendButtons(sock, from, T(session, 'choose'), [
                { id: 'home', text: `🏠 ${T(session, 'home_btn')}` }
            ]);
        }
    } catch (e) {
        console.error('Text order error:', e);
        await sendText(sock, from, `❌ ${T(session, 'list_menu_fallback_error')}`);
    }
}

async function showLiveBillOptions(sock, from, session) {
    // Clear tip info when paying bill
    delete session.tip_waiter_id;
    delete session.tip_waiter_name;
    session.quick_payment_mode = 'bill';
    session.quick_payment_desc = 'Bill Payment';

    // If no table number, we can't fetch an active order, so go straight to quick payment
    if (!session.table_number) {
        await showQuickPaymentAmount(sock, from, session);
        return;
    }

    try {
        const activeOrder = await api.getActiveOrder(session.restaurant_id, session.table_number);
        if (activeOrder.success && activeOrder.order && activeOrder.order.payment_status !== 'paid') {
            session.active_order_id = activeOrder.order.id;
            session.order_total = activeOrder.order.total;
            await showPaymentSummary(sock, from, session);
        } else {
            await showQuickPaymentAmount(sock, from, session);
        }
    } catch (e) {
        await showQuickPaymentAmount(sock, from, session);
    }
}



async function showQuickPaymentPhone(sock, from, session) {
    session.state = 'QUICK_PAYMENT_PHONE';
    const msg = session.tip_waiter_id
        ? `📱 ${T(session, 'enter_phone_tip')}`
        : session.quick_payment_mode === 'bill'
            ? `📱 ${T(session, 'enter_phone_billed')}`
            : `📱 ${T(session, 'enter_phone_pay')}`;
    await sendText(sock, from, msg);
}

async function handleQuickPaymentPhoneState(sock, from, session, text) {
    if (/^(0\d{9}|255\d{9})$/.test(text)) {
        session.ussd_phone = text.startsWith('255') ? '0' + text.slice(3) : text;
        await initiateQuickPayment(sock, from, session);
    } else {
        await sendText(sock, from, `❌ ${T(session, 'invalid_number')}`);
    }
}

async function showQuickPaymentAmount(sock, from, session) {
    session.state = 'QUICK_PAYMENT_AMOUNT';
    const msg = session.tip_waiter_id && session.tip_waiter_name
        ? `💰 ${T(session, 'tip_amount')} ${session.tip_waiter_name.toUpperCase()} (Tsh):`
        : `💰 ${T(session, 'enter_amount')}`;
    await sendText(sock, from, msg);
}

async function handleQuickPaymentAmountState(sock, from, session, text) {
    const amount = parseInt(text.replace(/,/g, ''));
    if (!isNaN(amount) && amount > 0) {
        session.quick_payment_amount = amount;
        await showQuickPaymentPhone(sock, from, session);
    } else {
        await sendText(sock, from, `❌ ${T(session, 'invalid_amount')}`);
    }
}

async function initiateQuickPayment(sock, from, session) {
    await sendText(sock, from, `🔄 ${T(session, 'sending_request')}`);
    try {
        const payload = {
            restaurant_id: session.restaurant_id,
            phone_number: session.ussd_phone,
            amount: session.quick_payment_amount,
            description: session.quick_payment_desc || 'Bill Payment',
            network: detectNetwork(session.ussd_phone)
        };
        if (session.tip_waiter_id) payload.waiter_id = session.tip_waiter_id;
        const result = await api.initiateQuickPayment(payload);

        if (result.success) {
            session.quick_payment_id = result.payment_id;
            session.state = 'QUICK_PAYMENT_PENDING';
            await sendButtons(sock, from,
                `✅ ${T(session, 'request_sent')} ${session.ussd_phone}!\n\n` +
                `Amount: ${session.quick_payment_amount}/=\n\n` +
                `${T(session, 'confirm_on_phone')}\n` +
                `✅ *${T(session, 'bot_confirm_auto')}*`,
                [
                    { id: 'home', text: `🏠 ${T(session, 'home')}` }
                ],
                '💳✨'
            );
            startPaymentPolling(sock, from, session, 'quick', result.payment_id);
        } else {
            await sendText(sock, from, `❌ ${T(session, 'quick_pay_issue')}`);
            await showHomeScreen(sock, from, session);
        }
    } catch (e) {
        console.error('Quick Payment Error:', e);
        await sendText(sock, from, `❌ ${T(session, 'quick_pay_issue')}`);
        await showHomeScreen(sock, from, session);
    }
}

async function handleQuickPaymentPendingState(sock, from, session, text) {
    if (text === 'check_status') {
        const result = await api.checkQuickPaymentStatus(session.quick_payment_id);
        if (result.success && result.status === 'paid') {
            await sendText(sock, from, '✅ Malipo yamethibitishwa! Asante.');
            await showHomeScreen(sock, from, session);
        } else {
            await sendText(sock, from, `⏳ Status: ${result.status || 'Pending'}. ${T(session, 'payment_waiting_sw')}`);
            await sendButtons(sock, from, `${T(session, 'chagua')}`, [
                { id: 'check_status', text: `🔄 ${T(session, 'check_tena')}` },
                { id: 'home', text: `🏠 ${T(session, 'home')}` }
            ]);
        }
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    }
}

async function showWaiterTipList(sock, from, session) {
    session.state = 'SELECT_WAITER_TIP';
    try {
        const result = await api.getWaiters(session.restaurant_id);
        if (result.success && result.data.length > 0) {
            const rows = result.data.map(w => ({
                id: `tip_waiter_${w.id}|${w.name}`,
                title: `👤 ${w.name}`,
                description: T(session, 'tip_row_desc')
            }));
            rows.push({ id: 'home', title: `🏠 ${T(session, 'home')}` });

            const tipTitle = session.lang === 'sw' ? T(session, 'tip_pick_stylist_sw') : T(session, 'tip_pick_stylist');
            await sendList(sock, from, `💝 ${tipTitle}`, T(session, 'waiters_list_btn'), [{ title: T(session, 'waiters_list_title'), rows }], '💝✨');
        } else {
            await sendText(sock, from, T(session, 'waiters_list_empty'));
            await showHomeScreen(sock, from, session);
        }
    } catch (e) {
        console.error('Fetch waiters error:', e);
        await showHomeScreen(sock, from, session);
    }
}

async function handleSelectWaiterTipState(sock, from, session, text) {
    if (text.startsWith('tip_waiter_')) {
        const parts = text.replace('tip_waiter_', '').split('|');
        session.tip_waiter_id = parts[0];
        session.tip_waiter_name = parts[1];
        session.quick_payment_mode = 'tip';
        session.quick_payment_desc = `Tip for ${session.tip_waiter_name}`;
        await showQuickPaymentAmount(sock, from, session);
    } else if (text === 'home') {
        await showHomeScreen(sock, from, session);
    }
}



// ═══════════════════════════════════════════════════════════════
// MESSAGE SENDERS
// ═══════════════════════════════════════════════════════════════

async function sendText(sock, from, text) {
    await sock.sendMessage(from, { text });
}

async function sendButtons(sock, from, text, buttons, headerEmoji = '✨') {
    const session = sessions[from];
    session.menu_options = {};
    let menuText = `━━━━━━━━ ${headerEmoji} ━━━━━━━━\n`;
    menuText += text + '\n\n';
    buttons.forEach((b, i) => {
        const key = (i + 1).toString();
        session.menu_options[key] = b.id;
        const numEmoji = getNumberEmoji(i + 1);
        menuText += `${numEmoji}${b.text}\n`;
    });
    menuText += '━━━━━━━━━━━━━━━━\n';
    menuText += '✅ReplyNumberToChoose';
    await sock.sendMessage(from, { text: menuText });
}

async function sendList(sock, from, text, buttonText, sections, headerEmoji = '✨') {
    const session = sessions[from];
    session.menu_options = {};
    let menuText = `━━━━━━━━${headerEmoji}━━━━━━━━\n`;
    menuText += text + '\n';
    let counter = 1;
    sections.forEach(section => {
        if (section.title) menuText += `${section.title}\n`;
        section.rows.forEach(row => {
            const key = counter.toString();
            session.menu_options[key] = row.id;
            const numEmoji = getNumberEmoji(counter);
            menuText += `${numEmoji}${row.title}`;
            // if (row.description) menuText += `(${row.description})`;
            menuText += '\n';
            counter++;
        });
    });
    menuText += '━━━━━━━━━━━━━━━━\n';
    menuText += '✅ReplyNumberToChoose';
    await sock.sendMessage(from, { text: menuText });
}

async function sendImageWithButtons(sock, from, imageUrl, caption, buttons, headerEmoji = '✨') {
    try {
        await sock.sendMessage(from, { image: { url: imageUrl }, caption: caption });
    } catch (e) {
        await sendText(sock, from, caption);
    }
    await sendButtons(sock, from, 'Choose:', buttons, headerEmoji);
}

function getNumberEmoji(num) {
    const emojis = ['0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟'];
    return emojis[num] || `*${num}.*`;
}

function detectNetwork(phone) {
    if (phone.startsWith('255')) phone = '0' + phone.slice(3);
    const prefix = phone.substring(0, 3);
    if (['074', '075', '076'].includes(prefix)) return 'vodacom';
    if (['065', '067', '071', '077'].includes(prefix)) return 'tigo';
    if (['068', '069', '078', '079'].includes(prefix)) return 'airtel';
    if (['062'].includes(prefix)) return 'halotel';
    return 'vodacom';
}

async function startPaymentPolling(sock, from, session, type, id) {
    let attempts = 0;
    const maxAttempts = 30; // 5 minutes (10s * 30)

    const interval = setInterval(async () => {
        attempts++;
        if (attempts > maxAttempts) {
            clearInterval(interval);
            return;
        }

        try {
            let result;
            if (type === 'order') {
                result = await api.getOrderStatus(id);
                if (result.payment_status === 'paid') {
                    await sendText(sock, from, T(session, 'payment_confirmed_poll'));
                    await showHomeScreen(sock, from, session);
                    clearInterval(interval);
                }
            } else {
                result = await api.checkQuickPaymentStatus(id);
                if (result.success && result.status === 'paid') {
                    await sendText(sock, from, T(session, 'payment_confirmed_poll'));
                    await showHomeScreen(sock, from, session);
                    clearInterval(interval);
                }
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }, 10000); // Check every 10 seconds
}

module.exports = { handleMessage, extractMessageText };
