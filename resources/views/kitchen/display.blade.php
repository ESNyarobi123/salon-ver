<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('salon.floor_display') }} · {{ $restaurant->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
        :root{--bg:#0a0514;--card:rgba(20,15,40,0.8);--border:rgba(255,255,255,0.08);--primary:#8b5cf6;--success:#10b981;--warning:#f59e0b;--danger:#ef4444}
        body{background:linear-gradient(135deg,var(--bg) 0%,#120a24 50%,#080310 100%);min-height:100vh;color:white}
        .orb{position:fixed;border-radius:50%;filter:blur(100px);opacity:0.15;pointer-events:none}
        .orb-1{width:600px;height:600px;background:var(--primary);top:-200px;right:-200px;animation:float 20s ease-in-out infinite}
        .orb-2{width:400px;height:400px;background:#06b6d4;bottom:-100px;left:-100px;animation:float 15s ease-in-out infinite reverse}
        @keyframes float{0%,100%{transform:translate(0,0)scale(1)}50%{transform:translate(30px,-30px)scale(1.1)}}
        .header{position:fixed;top:0;left:0;right:0;height:64px;background:rgba(10,5,20,0.9);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);z-index:100;display:flex;align-items:center;padding:0 24px}
        .header-content{width:100%;max-width:1920px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
        .brand{display:flex;align-items:center;gap:12px}
        .brand-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--primary) 0%,#06b6d4 100%);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(139,92,246,0.4);font-size:1.5rem}
        .brand h1{font-size:1.1rem;font-weight:800;background:linear-gradient(135deg,#fff 0%,rgba(255,255,255,0.7) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .brand span{font-size:0.65rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.15em}
        .stats{display:flex;gap:12px}
        .stat{display:flex;align-items:center;gap:10px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:100px}
        .stat.urgent{background:rgba(239,68,68,0.15);border-color:rgba(239,68,68,0.3);animation:pulse-red 2s ease-in-out infinite}
        @keyframes pulse-red{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0.4)}50%{box-shadow:0 0 20px 0 rgba(239,68,68,0.2)}}
        .stat-icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.9rem}
        .stat.urgent .stat-icon{background:rgba(239,68,68,0.3)}
        .stat.active .stat-icon{background:rgba(245,158,11,0.3)}
        .stat.pending .stat-icon{background:rgba(139,92,246,0.3)}
        .stat-value{font-size:1.25rem;font-weight:800}
        .stat-label{font-size:0.65rem;color:rgba(255,255,255,0.5);text-transform:uppercase}
        .clock{text-align:right}
        .clock-time{font-size:1.5rem;font-weight:800;background:linear-gradient(135deg,var(--primary) 0%,#06b6d4 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .clock-date{font-size:0.7rem;color:rgba(255,255,255,0.4);text-transform:uppercase}
        .main{padding:88px 24px 24px;position:relative;z-index:1}
        .grid{max-width:1920px;margin:0 auto;display:grid;grid-template-columns:1fr 360px;gap:24px}
        .section{margin-bottom:24px}
        .section-header{display:flex;align-items:center;gap:12px;margin-bottom:16px}
        .section-title{font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em}
        .section-count{padding:4px 12px;background:rgba(255,255,255,0.1);border-radius:100px;font-size:0.75rem;font-weight:700}
        .orders-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
        .card{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:all 0.3s ease;animation:slide-up 0.4s ease-out}
        .card.compact{padding:12px}
        .card.expanded .card-body{display:block}
        @keyframes slide-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .card:hover{transform:translateY(-2px);border-color:rgba(255,255,255,0.15);box-shadow:0 10px 30px rgba(0,0,0,0.3)}
        .card.urgent{border-color:rgba(239,68,68,0.4);animation:urgent-pulse 2s ease-in-out infinite}
        @keyframes urgent-pulse{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0.3)}50%{box-shadow:0 0 20px 0 rgba(239,68,68,0.2)}}
        .card.vip{border-color:rgba(245,158,11,0.4);background:linear-gradient(135deg,rgba(245,158,11,0.08) 0%,var(--card) 100%)}
        
        /* Compact Card Header */
        .compact-header{display:flex;align-items:center;justify-content:space-between;gap:12px;cursor:pointer}
        .compact-left{display:flex;align-items:center;gap:10px;flex:1}
        .table-num{width:40px;height:40px;background:linear-gradient(135deg,var(--primary) 0%,#06b6d4 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;box-shadow:0 4px 12px rgba(139,92,246,0.3);flex-shrink:0}
        .card.vip .table-num{background:linear-gradient(135deg,var(--warning) 0%,var(--danger) 100%)}
        .compact-info{flex:1;min-width:0}
        .compact-info h4{font-size:0.9rem;font-weight:700;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .compact-info .staff-line{font-size:0.75rem;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:4px}
        .item-summary{font-size:0.7rem;color:rgba(255,255,255,0.5);margin-top:2px}
        .compact-right{text-align:right;flex-shrink:0}
        .timer-compact{font-size:1.1rem;font-weight:800;line-height:1}
        .timer-compact.good{color:var(--success)}
        .timer-compact.warning{color:var(--warning)}
        .timer-compact.danger{color:var(--danger);animation:blink 1s ease-in-out infinite}
        .expand-btn{width:28px;height:28px;border-radius:6px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s ease;margin-left:8px}
        .expand-btn:hover{background:rgba(255,255,255,0.1);color:white}
        .card.expanded .expand-btn{transform:rotate(180deg)}
        
        /* Expanded Card Body */
        .card-body{display:none;padding:0 12px 12px;border-top:1px solid var(--border);margin-top:12px;padding-top:12px;animation:fadeIn 0.3s ease}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .card-body .item{display:flex;align-items:center;gap:10px;padding:8px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:8px;margin-bottom:6px;cursor:pointer;transition:all 0.2s ease}
        .card-body .item:hover{background:rgba(255,255,255,0.06)}
        .card-body .item-qty{width:28px;height:28px;background:rgba(139,92,246,0.2);border:1px solid rgba(139,92,246,0.3);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#c4b5fd;flex-shrink:0}
        .card-body .item-name{font-size:0.8rem;font-weight:600;flex:1}
        .card-body .item-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
        .card-body .item-dot.pending{background:var(--primary)}
        .card-body .item-dot.cooking{background:var(--warning)}
        .card-body .item-dot.ready{background:var(--success)}
        
        .compact-actions{display:flex;gap:8px;margin-top:12px;padding-top:12px;border-top:1px solid var(--border)}
        .btn-compact{flex:1;padding:8px;border:none;border-radius:8px;font-size:0.7rem;font-weight:700;text-transform:uppercase;cursor:pointer;transition:all 0.2s ease}
        .btn-compact.primary{background:linear-gradient(135deg,var(--primary) 0%,#7c3aed 100%);color:white}
        .btn-compact.success{background:linear-gradient(135deg,var(--success) 0%,#059669 100%);color:white}
        .btn-compact.secondary{background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.1)}
        .tab-btn{padding:10px 20px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;color:rgba(255,255,255,0.6);font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s ease}
        .tab-btn:hover{background:rgba(255,255,255,0.1);color:white}
        .tab-btn.active{background:linear-gradient(135deg,var(--primary) 0%,#7c3aed 100%);color:white;border-color:transparent;box-shadow:0 4px 15px rgba(139,92,246,0.3)}
        .history-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;transition:all 0.2s ease}
        .history-card:hover{border-color:rgba(255,255,255,0.15);transform:translateY(-2px)}
        .history-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
        .history-table{width:36px;height:36px;background:linear-gradient(135deg,var(--success) 0%,#059669 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800}
        .history-status{padding:4px 12px;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase}
        .history-status.ready{background:rgba(245,158,11,0.15);color:#fbbf24;border:1px solid rgba(245,158,11,0.3)}
        .history-status.served{background:rgba(6,182,212,0.15);color:#67e8f9;border:1px solid rgba(6,182,212,0.3)}
        .history-status.completed{background:rgba(16,185,129,0.15);color:#6ee7b7;border:1px solid rgba(16,185,129,0.3)}
        .history-items{margin-top:12px;padding-top:12px;border-top:1px solid var(--border)}
        .history-item{display:flex;align-items:center;gap:8px;padding:6px 0;font-size:0.8rem;color:rgba(255,255,255,0.8)}
        .history-footer{display:flex;align-items:center;justify-content:space-between;margin-top:12px;padding-top:12px;border-top:1px solid var(--border);font-size:0.75rem;color:rgba(255,255,255,0.5)}
        .ready-box{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:16px}
        .ready-box h2{font-size:0.8rem;font-weight:700;text-transform:uppercase;color:var(--success);margin-bottom:16px;display:flex;align-items:center;gap:8px}
        .ready-box h2::before{content:'';width:8px;height:8px;background:var(--success);border-radius:50%;animation:pulse 2s ease-in-out infinite}
        .ready-item{display:flex;align-items:center;gap:12px;padding:12px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);border-radius:12px;cursor:pointer;transition:all 0.2s ease;margin-bottom:10px}
        .ready-item:hover{background:rgba(16,185,129,0.15);border-color:rgba(16,185,129,0.3);transform:translateX(4px)}
        .ready-table{width:36px;height:36px;background:linear-gradient(135deg,var(--success) 0%,#059669 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800}
        .ready-info h4{font-size:0.85rem;font-weight:600}
        .ready-info span{font-size:0.7rem;color:rgba(255,255,255,0.5)}
        .ready-time{font-size:0.75rem;font-weight:700;color:var(--success);margin-left:auto}
        .empty{grid-column:1 / -1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px;text-align:center}
        .empty-icon{width:100px;height:100px;background:rgba(139,92,246,0.1);border:2px solid rgba(139,92,246,0.2);border-radius:24px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;color:rgba(139,92,246,0.5);font-size:3rem}
        .empty h2{font-size:1.5rem;font-weight:700;margin-bottom:8px}
        .empty p{color:rgba(255,255,255,0.5)}
        .connection{position:fixed;bottom:20px;right:20px;display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--card);border:1px solid var(--border);border-radius:100px;backdrop-filter:blur(10px);z-index:100}
        .connection-dot{width:8px;height:8px;border-radius:50%;background:var(--success);animation:pulse 2s ease-in-out infinite}
        .connection.disconnected .connection-dot{background:var(--danger);animation:none}
        .connection span{font-size:0.75rem;font-weight:600;color:rgba(255,255,255,0.7)}
        .toast{position:fixed;top:80px;right:24px;padding:16px 20px;background:linear-gradient(135deg,rgba(139,92,246,0.95) 0%,rgba(6,182,212,0.95) 100%);border-radius:12px;display:flex;align-items:center;gap:12px;z-index:200;transform:translateX(400px);transition:transform 0.4s ease;box-shadow:0 10px 40px rgba(139,92,246,0.4)}
        .toast.show{transform:translateX(0)}
        @media(max-width:1200px){.grid{grid-template-columns:1fr}.sidebar{display:none}}
        @media(max-width:768px){.stats{display:none}.orders-row{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    
    <header class="header">
        <div class="header-content">
            <div class="brand">
                <div class="brand-icon">💅</div>
                <div>
                    <h1>{{ config('salon.floor_display') }}</h1>
                    <span>{{ $restaurant->name }}</span>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat urgent" id="stat-overdue">
                    <div class="stat-icon">⚠️</div>
                    <div><div class="stat-value" id="overdue-count">0</div><div class="stat-label">Overdue</div></div>
                </div>
                <div class="stat active" id="stat-preparing">
                    <div class="stat-icon">🔥</div>
                    <div><div class="stat-value" id="preparing-count">0</div><div class="stat-label">In service</div></div>
                </div>
                <div class="stat pending" id="stat-pending">
                    <div class="stat-icon">⏳</div>
                    <div><div class="stat-value" id="pending-count">0</div><div class="stat-label">Pending</div></div>
                </div>
            </div>
            
            <div class="clock">
                <div class="clock-time" id="clock">00:00</div>
                <div class="clock-date" id="date">Loading...</div>
            </div>
        </div>
    </header>
    
    <main class="main">
        <!-- Tab Navigation -->
        <div class="tabs" style="display:flex;gap:8px;margin-bottom:20px;max-width:1920px;margin:0 auto 20px;padding:0 24px">
            <button class="tab-btn active" id="tab-active" onclick="switchTab('active')">
                🔥 Active bookings
            </button>
            <button class="tab-btn" id="tab-history" onclick="switchTab('history')">
                📜 Booking history
            </button>
        </div>
        
        <!-- Active bookings view -->
        <div id="view-active" class="view active">
            <div class="grid">
                <div id="orders-container">
                    <div class="section" id="urgent-section" style="display:none">
                        <div class="section-header">
                            <span class="section-title" style="color:var(--danger)">⚠️ Urgent</span>
                            <span class="section-count" style="background:rgba(239,68,68,0.2);color:#fca5a5" id="urgent-count">0</span>
                        </div>
                        <div class="orders-row" id="urgent-orders"></div>
                    </div>
                    
                    <div class="section" id="cooking-section">
                        <div class="section-header">
                            <span class="section-title" style="color:var(--warning)">🔥 In progress</span>
                            <span class="section-count" style="background:rgba(245,158,11,0.2);color:#fcd34d" id="cooking-count">0</span>
                        </div>
                        <div class="orders-row" id="cooking-orders"></div>
                    </div>
                    
                    <div class="section" id="pending-section">
                        <div class="section-header">
                            <span class="section-title">📋 Pending</span>
                            <span class="section-count" id="pending-count-display">0</span>
                        </div>
                        <div class="orders-row" id="pending-orders"></div>
                    </div>
                    
                    <div class="empty" id="empty-state">
                        <div class="empty-icon">🍽️</div>
                        <h2>No active bookings</h2>
                        <p>New bookings appear automatically</p>
                    </div>
                </div>
                
                <div class="sidebar">
                    <div class="ready-box">
                        <h2>Ready for service</h2>
                        <div id="ready-list">
                            <div style="text-align:center;padding:40px;color:rgba(255,255,255,0.4)">
                                <p style="font-size:0.85rem">No bookings ready yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking history view -->
        <div id="view-history" class="view" style="display:block">
            <div style="max-width:1920px;margin:0 auto;padding:0 24px">
                <!-- Filter Bar -->
                <div class="filter-bar" style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center">
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="font-size:0.8rem;color:rgba(255,255,255,0.6)">📅 Date:</span>
                        <select id="history-date" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 12px;color:white;font-size:0.85rem;cursor:pointer" onchange="fetchHistory()">
                            <option value="">📅 Last 7 Days</option>
                            <option value="{{ date('Y-m-d') }}">📅 Today</option>
                            <option value="{{ date('Y-m-d', strtotime('-1 day')) }}">📅 Yesterday</option>
                        </select>
                    </div>
                    
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="font-size:0.8rem;color:rgba(255,255,255,0.6)">📊 Status:</span>
                        <select id="history-status" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 12px;color:white;font-size:0.85rem;cursor:pointer" onchange="fetchHistory()">
                            <option value="all">All Status</option>
                            <option value="ready">Ready</option>
                            <option value="served">Served</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="font-size:0.8rem;color:rgba(255,255,255,0.6)">🪑 {{ config('salon.seat') }}:</span>
                        <select id="history-table" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 12px;color:white;font-size:0.85rem;cursor:pointer" onchange="fetchHistory()">
                            <option value="all">All {{ config('salon.seat_plural') }}</option>
                        </select>
                    </div>
                    
                    <div style="margin-left:auto;display:flex;gap:12px;align-items:center">
                        <div class="stat-pill" style="background:rgba(16,185,129,0.15);border-color:rgba(16,185,129,0.3)">
                            <span style="font-size:0.75rem;color:rgba(255,255,255,0.6)">Total:</span>
                            <span id="history-total" style="font-size:1rem;font-weight:700;color:var(--success)">0</span>
                        </div>
                    </div>
                </div>
                
                <!-- History Stats -->
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
                    <div class="stat-card" style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center">
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);text-transform:uppercase;margin-bottom:4px">Total bookings</div>
                        <div id="stat-total" style="font-size:1.5rem;font-weight:800">0</div>
                    </div>
                    <div class="stat-card" style="background:linear-gradient(135deg,rgba(245,158,11,0.15) 0%,rgba(245,158,11,0.05) 100%);border:1px solid rgba(245,158,11,0.3);border-radius:12px;padding:16px;text-align:center">
                        <div style="font-size:0.75rem;color:#fcd34d;text-transform:uppercase;margin-bottom:4px">Ready</div>
                        <div id="stat-ready" style="font-size:1.5rem;font-weight:800;color:#fbbf24">0</div>
                    </div>
                    <div class="stat-card" style="background:linear-gradient(135deg,rgba(6,182,212,0.15) 0%,rgba(6,182,212,0.05) 100%);border:1px solid rgba(6,182,212,0.3);border-radius:12px;padding:16px;text-align:center">
                        <div style="font-size:0.75rem;color:#67e8f9;text-transform:uppercase;margin-bottom:4px">Served</div>
                        <div id="stat-served" style="font-size:1.5rem;font-weight:800;color:#22d3ee">0</div>
                    </div>
                    <div class="stat-card" style="background:linear-gradient(135deg,rgba(16,185,129,0.15) 0%,rgba(16,185,129,0.05) 100%);border:1px solid rgba(16,185,129,0.3);border-radius:12px;padding:16px;text-align:center">
                        <div style="font-size:0.75rem;color:#6ee7b7;text-transform:uppercase;margin-bottom:4px">Completed</div>
                        <div id="stat-completed" style="font-size:1.5rem;font-weight:800;color:#34d399">0</div>
                    </div>
                </div>
                
                <!-- History List -->
                <div id="history-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:16px">
                    <div class="empty" style="grid-column:1/-1">
                        <div class="empty-icon">📜</div>
                        <h2>Loading history…</h2>
                        <p id="history-debug">Please wait...</p>
                    </div>
                </div>
                
                <!-- Debug Panel (hidden by default, press D to show) -->
                <div id="debug-panel" style="display:none;position:fixed;bottom:20px;right:20px;background:rgba(0,0,0,0.9);border:1px solid var(--primary);border-radius:12px;padding:16px;max-width:400px;max-height:300px;overflow:auto;z-index:9999">
                    <div style="font-size:0.8rem;color:var(--primary);margin-bottom:8px">Debug Info</div>
                    <pre id="debug-content" style="font-size:0.7rem;color:white;margin:0"></pre>
                </div>
            </div>
        </div>
    </main>
    
    <div class="connection" id="connection-status">
        <div class="connection-dot"></div>
        <span>Live Updates</span>
    </div>
    
    <div class="toast" id="toast">
        <span>🔔</span>
        <span id="toast-message">New booking!</span>
    </div>
    
    <script>
        const apiUrl="{{ route('kitchen.api.orders', $restaurant->kitchen_token) }}";
        const apiHistoryUrl="{{ route('kitchen.api.history', $restaurant->kitchen_token) }}";
        const apiOrderStatusUrl="{{ route('kitchen.api.order.status', $restaurant->kitchen_token) }}";
        const apiItemStatusUrl="{{ route('kitchen.api.item.status', $restaurant->kitchen_token) }}";
        let previousOrderIds=[];
        let currentTab='active';

        function switchTab(tab){
            currentTab=tab;
            document.querySelectorAll('.tab-btn').forEach(btn=>btn.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');
            
            // Toggle views using display style
            const activeView = document.getElementById('view-active');
            const historyView = document.getElementById('view-history');
            
            if(tab === 'active') {
                activeView.style.display = 'block';
                historyView.style.display = 'none';
            } else {
                activeView.style.display = 'none';
                historyView.style.display = 'block';
                fetchHistory();
            }
        }

        async function fetchHistory(){
            const debugPanel=document.getElementById('debug-content');
            const debugMsg=document.getElementById('history-debug');
            
            try{
                const dateSelect=document.getElementById('history-date');
                const date=dateSelect?dateSelect.value:'';
                const status=document.getElementById('history-status').value;
                const table=document.getElementById('history-table').value;
                
                let url=apiHistoryUrl;
                const params=[];
                if(date)params.push(`date=${date}`);
                if(status!=='all')params.push(`status=${status}`);
                if(table!=='all')params.push(`table=${table}`);
                if(params.length>0)url+='?'+params.join('&');
                
                console.log('Fetching history from:', url);
                if(debugPanel)debugPanel.textContent=`Fetching: ${url}\n`;
                if(debugMsg)debugMsg.textContent='Fetching data...';
                
                const response=await fetch(url);
                const data=await response.json();
                
                console.log('History response:', data);
                if(debugPanel)debugPanel.textContent+=`Response: ${JSON.stringify(data, null, 2)}`;
                
                if(data.success){
                    renderHistory(data.orders,data.stats);
                    updateTableFilter(data.tables);
                    console.log(`Loaded ${data.orders.length} bookings from history`);
                    if(debugMsg)debugMsg.textContent=`Loaded ${data.orders.length} bookings`;
                }else{
                    console.error('History fetch failed:', data);
                    if(debugMsg)debugMsg.textContent='Failed: ' + (data.message || 'Unknown error');
                    showToast('Failed to load history');
                }
            }catch(error){
                console.error('Failed to fetch history:',error);
                if(debugPanel)debugPanel.textContent+=`Error: ${error.message}`;
                if(debugMsg)debugMsg.textContent='Error: ' + error.message;
                showToast('Error loading history');
            }
        }

        function renderHistory(orders,stats){
            console.log('renderHistory called with', orders.length, 'bookings');
            console.log('First booking:', orders[0]);
            
            document.getElementById('stat-total').textContent=stats.total;
            document.getElementById('stat-ready').textContent=stats.ready;
            document.getElementById('stat-served').textContent=stats.served;
            document.getElementById('stat-completed').textContent=stats.completed;
            document.getElementById('history-total').textContent=stats.total;
            
            const container=document.getElementById('history-list');
            if(!container){
                console.error('history-list container not found!');
                return;
            }
            
            if(orders.length===0){
                container.innerHTML=`<div class="empty" style="grid-column:1/-1">
                    <div class="empty-icon">📜</div>
                        <h2>No booking history</h2>
                    <p>No bookings found for selected filters</p>
                </div>`;
                return;
            }
            
            try {
                const html = orders.map((order, index)=>{
                    console.log(`Processing booking ${index}:`, order.id, order.table_number);
                    
                    // Handle items - could be array or object
                    let items = [];
                    if (order.items) {
                        if (Array.isArray(order.items)) {
                            items = order.items;
                        } else if (typeof order.items === 'object') {
                            items = Object.values(order.items);
                        }
                    }
                    const itemCount = items.length;
                    
                    const safeTable = order.table_number || 'N/A';
                    const safeWaiter = order.waiter_name || 'Stylist';
                    const safeStatus = order.status || 'unknown';
                    const safeAmount = order.total_amount || 0;
                    const safeTime = order.completed_at || 'N/A';
                    const safeTimeAgo = order.completed_time || '';
                    
                    return `<div class="history-card">
                        <div class="history-header">
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="history-table">${safeTable}</div>
                                <div>
                                    <div style="font-size:0.9rem;font-weight:700">Seat ${safeTable}</div>
                                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.5)">👤 ${safeWaiter}</div>
                                </div>
                            </div>
                            <div class="history-status ${safeStatus}">${safeStatus}</div>
                        </div>
                        ${itemCount>0?`<div class="history-items">
                            ${items.slice(0,3).map(item=>`
                                <div class="history-item">
                                    <span style="font-weight:600;color:var(--primary)">${item.quantity||1}x</span>
                                    <span>${item.name||'Unknown Item'}</span>
                                </div>
                            `).join('')}
                            ${itemCount>3?`<div style="font-size:0.75rem;color:rgba(255,255,255,0.4);font-style:italic">+ ${itemCount-3} more items</div>`:''}
                        </div>`:`<div class="history-items" style="color:rgba(255,255,255,0.4);font-style:italic;font-size:0.8rem">No items</div>`}
                        <div class="history-footer">
                            <span>💰 TSh ${parseInt(safeAmount).toLocaleString()}</span>
                            <span>✓ ${safeTime} ${safeTimeAgo?`(${safeTimeAgo})`:''}</span>
                        </div>
                    </div>`;
                }).join('');
                
                console.log('Generated HTML length:', html.length);
                container.innerHTML = html;
                console.log('HTML inserted into container');
            } catch (error) {
                console.error('Error rendering history:', error);
                container.innerHTML = `<div style="color:red;padding:20px">Error: ${error.message}</div>`;
            }
        }

        function updateTableFilter(tables){
            const select=document.getElementById('history-table');
            const currentValue=select.value;
            select.innerHTML='<option value="all">All seats</option>';
            tables.forEach(table=>{
                const option=document.createElement('option');
                option.value=table;
                option.textContent=`Seat ${table}`;
                select.appendChild(option);
            });
            select.value=currentValue;
        }

        function updateClock(){
            const now=new Date();
            document.getElementById('clock').textContent=now.toLocaleTimeString('en-US',{hour12:false,hour:'2-digit',minute:'2-digit'});
            document.getElementById('date').textContent=now.toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});
        }
        setInterval(updateClock,1000);
        updateClock();

        async function fetchOrders(){
            if(currentTab!=='active')return;
            try{
                const response=await fetch(apiUrl);
                const data=await response.json();
                if(data.success){
                    renderOrders(data.orders);
                    updateStats(data.stats);
                    document.getElementById('connection-status').classList.remove('disconnected');
                }
            }catch(error){
                document.getElementById('connection-status').classList.add('disconnected');
            }
        }

        function renderOrders(orders){
            const urgent=orders.filter(o=>o.sla_status==='red');
            const cooking=orders.filter(o=>o.status==='preparing');
            const pending=orders.filter(o=>o.status==='pending'||o.status==='confirmed');
            const ready=orders.filter(o=>o.status==='ready');

            const currentIds=orders.map(o=>o.id);
            const newOrders=currentIds.filter(id=>!previousOrderIds.includes(id));
            if(newOrders.length>0&&previousOrderIds.length>0){
                showToast(`${newOrders.length} new booking${newOrders.length>1?'s':''}!`);
            }
            previousOrderIds=currentIds;

            document.getElementById('urgent-section').style.display=urgent.length?'block':'none';
            document.getElementById('cooking-section').style.display=cooking.length?'block':'none';
            document.getElementById('pending-section').style.display=pending.length?'block':'none';
            document.getElementById('empty-state').style.display=orders.length?'none':'flex';

            document.getElementById('urgent-count').textContent=urgent.length;
            document.getElementById('cooking-count').textContent=cooking.length;
            document.getElementById('pending-count-display').textContent=pending.length;

            document.getElementById('urgent-orders').innerHTML=urgent.map(o=>renderCard(o,true)).join('');
            document.getElementById('cooking-orders').innerHTML=cooking.map(o=>renderCard(o)).join('');
            document.getElementById('pending-orders').innerHTML=pending.map(o=>renderCard(o)).join('');

            document.getElementById('ready-list').innerHTML=ready.length
                ?ready.map(o=>renderReady(o)).join('')
                :'<div style="text-align:center;padding:40px;color:rgba(255,255,255,0.4)"><p style="font-size:0.85rem">No bookings ready yet</p></div>';
        }

        function renderCard(order,isUrgent=false){
            const timerClass=order.sla_status==='red'?'danger':order.sla_status==='yellow'?'warning':'good';
            const cardClass=isUrgent?'urgent':order.is_vip?'vip':'';
            const totalItems=order.items.length;
            const pendingItems=order.items.filter(i=>i.status==='pending').length;
            const cookingItems=order.items.filter(i=>i.status==='cooking').length;
            const readyItems=order.items.filter(i=>i.status==='ready').length;
            
            // Summary text
            let summary=[];
            if(pendingItems>0)summary.push(`${pendingItems} pending`);
            if(cookingItems>0)summary.push(`${cookingItems} in progress`);
            if(readyItems>0)summary.push(`${readyItems} ready`);
            
            return `<div class="card compact ${cardClass}" id="card-${order.id}" data-order-id="${order.id}">
                <div class="compact-header" onclick="toggleExpand(${order.id})">
                    <div class="compact-left">
                        <div class="table-num">${order.table_number}</div>
                        <div class="compact-info">
                            <h4>Seat ${order.table_number}</h4>
                            <div class="staff-line">
                                👤 ${order.waiter_name||'No stylist'}
                            </div>
                            <div class="item-summary">
                                ${totalItems} items • ${summary.join(' • ')||'All pending'}
                            </div>
                        </div>
                    </div>
                    <div class="compact-right" style="display:flex;align-items:center">
                        <div class="timer-compact ${timerClass}">${order.elapsed_time}</div>
                        <div class="expand-btn">▼</div>
                    </div>
                </div>
                
                <div class="card-body" id="body-${order.id}">
                    ${order.is_vip?`<div class="vip-badge" style="margin-bottom:10px">⭐ VIP booking</div>`:''}
                    <div class="items-list">
                        ${order.items.map(item=>`<div class="item ${item.status}" onclick="toggleItemStatus('${item.id}','${item.status}')">
                            <div class="item-qty">${item.quantity}</div>
                            <div class="item-name">${item.name}</div>
                            <div class="item-dot ${item.status}"></div>
                        </div>`).join('')}
                    </div>
                    <div class="compact-actions">
                        ${order.status==='pending'||order.status==='confirmed'?`<button class="btn-compact primary" onclick="event.stopPropagation();updateOrderStatus(${order.id},'preparing')">▶ Start</button>`:''}
                        ${order.status==='preparing'?`<button class="btn-compact success" onclick="event.stopPropagation();updateOrderStatus(${order.id},'ready')">✓ Ready</button>`:''}
                        <button class="btn-compact secondary" onclick="event.stopPropagation();updateOrderStatus(${order.id},'cancelled')">✕</button>
                    </div>
                </div>
            </div>`;
        }

        function toggleExpand(orderId){
            const card=document.getElementById(`card-${orderId}`);
            card.classList.toggle('expanded');
        }

        function renderReady(order){
            return `<div class="ready-item" onclick="updateOrderStatus(${order.id},'served')">
                <div class="ready-table">${order.table_number}</div>
                <div class="ready-info">
                    <h4>Seat ${order.table_number}</h4>
                    <span>${order.items.length} items</span>
                </div>
                <div class="ready-time">${order.elapsed_time}</div>
            </div>`;
        }

        function updateStats(stats){
            document.getElementById('overdue-count').textContent=stats.overdue||0;
            document.getElementById('preparing-count').textContent=stats.preparing||0;
            document.getElementById('pending-count').textContent=stats.pending||0;
        }

        async function updateOrderStatus(orderId,status){
            try{
                const response=await fetch(apiOrderStatusUrl,{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"').content},
                    body:JSON.stringify({order_id:orderId,status:status})
                });
                const data=await response.json();
                if(data.success){
                    fetchOrders();
                    showToast(`Booking ${status}!`);
                }
            }catch(error){}
        }

        async function toggleItemStatus(itemId,currentStatus){
            const flow={'pending':'cooking','cooking':'ready','ready':'pending'};
            const newStatus=flow[currentStatus]||'cooking';
            try{
                const response=await fetch(apiItemStatusUrl,{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"').content},
                    body:JSON.stringify({item_id:itemId,status:newStatus})
                });
                const data=await response.json();
                if(data.success)fetchOrders();
            }catch(error){}
        }

        function showToast(message){
            const toast=document.getElementById('toast');
            document.getElementById('toast-message').textContent=message;
            toast.classList.add('show');
            setTimeout(()=>toast.classList.remove('show'),3000);
        }

        fetchOrders();
        setInterval(fetchOrders,5000);
        
        // Set initial view states
        document.getElementById('view-active').style.display = 'block';
        document.getElementById('view-history').style.display = 'none';
        
        // Keyboard shortcut: Press 'D' to show debug panel
        document.addEventListener('keydown', function(e) {
            if(e.key === 'd' || e.key === 'D') {
                const panel = document.getElementById('debug-panel');
                if(panel) {
                    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
                }
            }
        });
    </script>
</body>
</html>
