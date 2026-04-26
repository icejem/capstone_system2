<!-- AI Chatbot Widget -->
<div id="ai-chatbot-widget" class="ai-chatbot-container">
    <!-- Chatbot Button -->
    <div id="chatbot-toggle" class="chatbot-toggle-btn" title="Open AI Assistant">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
        </svg>
    </div>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window hidden">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-content">
                <h3>System AI Assistant</h3>
                <p>Powered by Intelligent System</p>
            </div>
            <button id="chatbot-close" class="chatbot-close-btn" aria-label="Close chatbot">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Messages Container -->
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chatbot-message bot-message initial-message">
                <div class="message-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                    </svg>
                </div>
                <div class="message-content">
                    <p><strong>Welcome!</strong> I'm your System AI Assistant. I can help you with:</p>
                    <ul>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:6px;vertical-align:middle;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.03-2.71c-.3-.4-.92-.41-1.23 0-.32.41-.11 1.05.29 1.35l2.83 3.78c.17.23.45.36.76.36.31 0 .59-.13.76-.36l3.56-4.56c.4-.53.15-1.28-.38-1.67-.52-.39-1.28-.15-1.68.37z"></path></svg> Platform overview</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:6px;vertical-align:middle;"><path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5v6H20zM2 4v14h4V4H2zm4.5 6.5C6.5 9.57 7.58 9 9 9s2.5.57 2.5 1.5S10.42 12 9 12s-2.5.43-2.5-1.5zM18.5 4c-1.41 0-2.5.67-2.5 1.5V14H14V5.5c0-2.45 2.09-4.5 4.5-4.5S23 3.05 23 5.5v8.5h-2v-3h-2.5V4z"></path></svg> Student features</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:6px;vertical-align:middle;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg> Instructor tools</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:6px;vertical-align:middle;"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"></path></svg> Video calls</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:6px;vertical-align:middle;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg> Troubleshooting</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:6px;vertical-align:middle;"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></svg> Email alerts</li>
                    </ul>
                    <p style="margin-top: 12px;"><strong>Ask me anything!</strong></p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div id="chatbot-quick-actions" class="chatbot-quick-actions">
            <button class="quick-action-btn" data-question="How do I book a consultation?"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="margin-right:4px;vertical-align:middle;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"></path></svg> Book</button>
            <button class="quick-action-btn" data-question="How does the video call work?"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="margin-right:4px;vertical-align:middle;"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"></path></svg> Video</button>
            <button class="quick-action-btn" data-question="What are the system features?"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="margin-right:4px;vertical-align:middle;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg> Features</button>
            <button class="quick-action-btn" data-question="How do I reset my password?"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="margin-right:4px;vertical-align:middle;"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5s-5 2.24-5 5v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path></svg> Password</button>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <input 
                type="text" 
                id="chatbot-input" 
                class="chatbot-input" 
                placeholder="Ask me anything..."
                autocomplete="off"
            >
            <button id="chatbot-send" class="chatbot-send-btn" aria-label="Send message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16.6915026,12.4744748 L3.50612381,13.2599618 C3.19218622,13.2599618 3.03521743,13.4170592 3.03521743,13.5741566 L1.15159189,20.0151496 C0.8376543,20.8006365 0.99,21.89 1.77946707,22.52 C2.41,22.99 3.50612381,23.1 4.13399899,22.8429026 L21.714504,14.0454487 C22.6563168,13.5741566 23.1272231,12.6315722 22.9702544,11.6889879 L4.13399899,1.16346272 C3.50612381,-0.1 2.40999899,0.0570974149 1.77946707,0.4744748 C0.994623095,1.10604706 0.837654326,2.0486314 1.15159189,2.95121575 L3.03521743,9.39221275 C3.03521743,9.54931018 3.34915502,9.70640761 3.50612381,9.70640761 L16.6915026,10.4918945 C16.6915026,10.4918945 17.1624089,10.4918945 17.1624089,9.99788373 L17.1624089,11.1000636 C17.1624089,11.4744748 16.6915026,12.4744748 16.6915026,12.4744748 Z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
/* ════════════════════════════════════════
   AI CHATBOT STYLES
   ════════════════════════════════════════ */

.ai-chatbot-container {
    --chatbot-primary: #2d65ea;
    --chatbot-primary-dark: #1e3a8a;
    --chatbot-secondary: #60a5fa;
    --chatbot-bg: #0a1628;
    --chatbot-bg-light: #1e293b;
    --chatbot-border: rgba(59, 130, 246, 0.3);
    --chatbot-text: #e2e8f0;
    --chatbot-muted: #94a3b8;
    --chatbot-success: #10b981;
    --chatbot-warning: #f59e0b;
    --chatbot-error: #ef4444;
}

/* ── Toggle Button ── */
.chatbot-toggle-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--chatbot-primary) 0%, var(--chatbot-primary-dark) 100%);
    color: #fff;
    border: 2px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 32px rgba(45, 101, 234, 0.3);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 1200;
    animation: pulse-chatbot 2s ease-in-out infinite;
}

.chatbot-toggle-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 12px 48px rgba(45, 101, 234, 0.4);
}

.chatbot-toggle-btn:active {
    transform: scale(0.95);
}

@keyframes pulse-chatbot {
    0%, 100% { box-shadow: 0 8px 32px rgba(45, 101, 234, 0.3); }
    50% { box-shadow: 0 8px 32px rgba(45, 101, 234, 0.5); }
}

/* ── Chat Window ── */
.chatbot-window {
    position: fixed;
    bottom: 100px;
    right: 24px;
    width: 320px;
    max-height: 520px;
    background: var(--chatbot-bg);
    border: 1px solid var(--chatbot-border);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 1200;
    animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    backdrop-filter: blur(10px);
}

.chatbot-window.hidden {
    display: none;
    animation: none;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ── Header ── */
.chatbot-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: linear-gradient(135deg, rgba(45, 101, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    border-bottom: 1px solid var(--chatbot-border);
}

.chatbot-header-content h3 {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    margin-bottom: 4px;
}

.chatbot-header-content p {
    font-size: 12px;
    color: var(--chatbot-secondary);
    margin: 0;
    font-weight: 500;
}

.chatbot-close-btn {
    background: none;
    border: none;
    color: var(--chatbot-muted);
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.chatbot-close-btn:hover {
    color: #fff;
}

/* ── Messages ── */
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px 12px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chatbot-messages::-webkit-scrollbar {
    width: 6px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: rgba(45, 101, 234, 0.1);
    border-radius: 3px;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: var(--chatbot-secondary);
    border-radius: 3px;
}

.chatbot-message {
    display: flex;
    gap: 10px;
    animation: fadeInMessage 0.3s ease-out;
}

@keyframes fadeInMessage {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--chatbot-primary) 0%, var(--chatbot-secondary) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}

.message-content {
    flex: 1;
    padding: 12px 14px;
    background: var(--chatbot-bg-light);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.6;
    color: var(--chatbot-text);
    word-wrap: break-word;
}

.message-content p {
    margin: 0 0 8px 0;
}

.message-content ul {
    margin: 8px 0;
    padding-left: 20px;
    list-style-type: none;
}

.message-content li {
    margin: 6px 0;
}

.message-content a {
    color: var(--chatbot-secondary);
    text-decoration: none;
    transition: color 0.2s;
}

.message-content a:hover {
    color: #fff;
    text-decoration: underline;
}

.bot-message {
    justify-content: flex-start;
}

.user-message {
    justify-content: flex-end;
}

.user-message .message-content {
    background: linear-gradient(135deg, var(--chatbot-primary) 0%, var(--chatbot-primary-dark) 100%);
    color: #fff;
    border: none;
}

.initial-message {
    display: flex !important;
}

/* ── Quick Actions ── */
.chatbot-quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--chatbot-border);
}

.quick-action-btn {
    padding: 10px 12px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid var(--chatbot-border);
    border-radius: 8px;
    color: var(--chatbot-secondary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-action-btn:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: var(--chatbot-secondary);
    transform: translateY(-2px);
}

.quick-action-btn:active {
    transform: translateY(0);
}

/* ── Input Area ── */
.chatbot-input-area {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--chatbot-border);
    background: var(--chatbot-bg);
}

.chatbot-input {
    flex: 1;
    padding: 10px 14px;
    background: var(--chatbot-bg-light);
    border: 1px solid var(--chatbot-border);
    border-radius: 8px;
    color: var(--chatbot-text);
    font-size: 13px;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
}

.chatbot-input::placeholder {
    color: var(--chatbot-muted);
}

.chatbot-input:focus {
    border-color: var(--chatbot-secondary);
}

.chatbot-send-btn {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--chatbot-primary) 0%, var(--chatbot-primary-dark) 100%);
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}

.chatbot-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(45, 101, 234, 0.3);
}

.chatbot-send-btn:active {
    transform: scale(0.95);
}

/* ── Typing Indicator ── */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 14px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--chatbot-secondary);
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        opacity: 0.3;
    }
    30% {
        opacity: 1;
    }
}

/* ── Mobile Responsive ── */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 32px);
        max-height: calc(100vh - 140px);
        right: 16px;
        bottom: 80px;
    }

    .chatbot-quick-actions {
        grid-template-columns: 1fr;
    }

    .chatbot-toggle-btn {
        right: 16px;
        bottom: 16px;
        width: 48px;
        height: 48px;
    }
}

@media (max-width: 380px) {
    .chatbot-window {
        border-radius: 12px;
        max-height: calc(100vh - 100px);
    }

    .message-content {
        font-size: 12px;
    }
}
</style>

<script>
// ════════════════════════════════════════
// AI CHATBOT LOGIC
// ════════════════════════════════════════

class SystemAIChatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.isTyping = false;
        this.conversationHistory = [];
        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
        this.loadSystemKnowledge();
    }

    cacheDOM() {
        this.toggleBtn = document.getElementById('chatbot-toggle');
        this.window = document.getElementById('chatbot-window');
        this.closeBtn = document.getElementById('chatbot-close');
        this.messagesContainer = document.getElementById('chatbot-messages');
        this.input = document.getElementById('chatbot-input');
        this.sendBtn = document.getElementById('chatbot-send');
        this.quickActions = document.querySelectorAll('.quick-action-btn');
    }

    bindEvents() {
        this.toggleBtn.addEventListener('click', () => this.toggle());
        this.closeBtn.addEventListener('click', () => this.close());
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        this.quickActions.forEach(btn => {
            btn.addEventListener('click', () => {
                const question = btn.getAttribute('data-question');
                this.input.value = question;
                this.sendMessage();
            });
        });
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        this.window.classList.remove('hidden');
        this.input.focus();
    }

    close() {
        this.isOpen = false;
        this.window.classList.add('hidden');
    }

    loadSystemKnowledge() {
        // System Information Database
        this.knowledgeBase = {
            // ────────────────────────────────────────
            // GENERAL INFORMATION
            // ────────────────────────────────────────
            'system': {
                'name': 'Consultation Platform',
                'purpose': 'A professional online consultation system connecting students with instructors for real-time video consultations',
                'features': [
                    'Real-time video consultation sessions',
                    'Instructor availability scheduling',
                    'Automated email notifications',
                    'Consultation history and feedback',
                    'Secure authentication with MFA',
                    'WebRTC-based video technology'
                ],
                'technologies': [
                    'Laravel (Backend Framework)',
                    'PHP (Server-side Logic)',
                    'JavaScript (Frontend Interactivity)',
                    'WebRTC (Video Communication)',
                    'SQLite (Database)',
                    'Tailwind CSS (Styling)',
                    'Blade Templating (Views)'
                ]
            },

            // ────────────────────────────────────────
            // USER ROLES
            // ────────────────────────────────────────
            'roles': {
                'student': {
                    'description': 'User seeking consultation from instructors',
                    'capabilities': [
                        'Browse available instructors and their schedules',
                        'Request consultations during available time slots',
                        'Join video consultations with secure links',
                        'View consultation history and feedback',
                        'Cancel consultations if needed',
                        'Receive email notifications about consultations',
                        'Provide feedback after consultations',
                        'Manage account and preferences'
                    ],
                    'workflow': '1. Register account → 2. Browse instructors → 3. Select time slot → 4. Submit request → 5. Wait for approval → 6. Join video call → 7. Provide feedback'
                },
                'instructor': {
                    'description': 'Professional providing consultations to students',
                    'capabilities': [
                        'Set availability schedule for consultations',
                        'View consultation requests from students',
                        'Accept or decline consultation requests',
                        'Conduct video consultations with students',
                        'View consultation history',
                        'Manage profile and expertise areas',
                        'Receive email alerts for new requests',
                        'View feedback from students'
                    ],
                    'workflow': '1. Register account → 2. Set availability → 3. Receive requests → 4. Accept/Decline → 5. Conduct consultation → 6. View feedback'
                },
                'admin': {
                    'description': 'System administrator managing platform operations',
                    'capabilities': [
                        'Manage user accounts (create, edit, delete)',
                        'Monitor all consultations',
                        'Send system notifications',
                        'View platform statistics and analytics',
                        'Manage system settings',
                        'Handle disputes and complaints'
                    ]
                }
            },

            // ────────────────────────────────────────
            // FEATURES & HOW-TO
            // ────────────────────────────────────────
            'features': {
                'consultation': {
                    'booking': {
                        'title': 'How to Book a Consultation',
                        'steps': [
                            '1. Log in to your student account',
                            '2. Navigate to "Browse Instructors" or search by specialty',
                            '3. Click on an instructor to view their profile and availability',
                            '4. Select your preferred date and time slot',
                            '5. Fill in the consultation details (topic, concerns, etc.)',
                            '6. Review and submit your request',
                            '7. Wait for instructor approval (usually within a few minutes)',
                            '8. You\'ll receive an email confirmation when approved',
                            '9. Join the video call at the scheduled time using the provided link'
                        ],
                        'duration': 'Consultations typically last 30-60 minutes depending on instructor availability',
                        'cost': 'Please contact administration for pricing information',
                        'cancellation': 'Students can cancel up to 24 hours before the consultation with full refund'
                    },
                    'video_call': {
                        'title': 'How Video Calls Work',
                        'technology': 'We use WebRTC technology for secure, high-quality peer-to-peer video calls',
                        'requirements': [
                            'Stable internet connection (at least 2 Mbps upload/download)',
                            'Webcam and microphone',
                            'Modern web browser (Chrome, Firefox, Safari, Edge)',
                            'Permissions for camera and microphone access'
                        ],
                        'preparation': [
                            '1. Test your audio/video before the session',
                            '2. Join 5 minutes early',
                            '3. Ensure good lighting and quiet environment',
                            '4. Have any documents or notes ready',
                            '5. Check internet connection stability'
                        ],
                        'during_call': [
                            'Click "Start Video" button to activate camera',
                            'Share screen if needed to show presentations or documents',
                            'Use chat for quick messages',
                            'Take notes during consultation',
                            'Ask questions anytime'
                        ],
                        'after_call': [
                            '1. Provide feedback on the consultation',
                            '2. Download session transcript (if available)',
                            '3. Rate the instructor',
                            '4. Schedule follow-up if needed'
                        ],
                        'troubleshooting': {
                            'no_video': 'Check camera permissions in browser settings. Click camera icon and allow access.',
                            'no_audio': 'Check microphone permissions. Test mic volume in device settings.',
                            'lag': 'Close other apps using internet. Move closer to WiFi router. Reduce video quality.',
                            'connection_lost': 'The system auto-reconnects. If it persists, refresh the page or rejoin the call.',
                            'other_person_cannot_see_me': 'Ensure camera is enabled and not blocked. Check browser permissions.'
                        }
                    },
                    'cancellation': {
                        'title': 'Cancellation & Rescheduling',
                        'student': 'Click "Cancel Consultation" button. Admin and instructor will be notified by email.',
                        'instructor': 'Click "Decline" when a student requests. Automatic email sent to student.',
                        'refund_policy': 'Cancel at least 24 hours before for full refund. Cancellations within 24 hours may incur fees.',
                        'rescheduling': 'Cancel the existing consultation and book a new one at preferred time'
                    }
                },

                // ────────────────────────────────────────
                // INSTRUCTOR FEATURES
                // ────────────────────────────────────────
                'instructor_features': {
                    'availability': {
                        'title': 'Setting Your Availability',
                        'steps': [
                            '1. Go to "Manage Availability" in your dashboard',
                            '2. Click "Add Availability Block"',
                            '3. Select day(s) of the week',
                            '4. Set start and end times',
                            '5. Optionally add a title (e.g., "Morning Sessions", "Expert Consultations")',
                            '6. Click "Save"',
                            '7. Repeat for different time blocks',
                            '8. Your availability is now visible to students'
                        ],
                        'managing': 'You can edit or delete availability blocks anytime. Changes take effect immediately.',
                        'recurring': 'Availability blocks can be set as recurring (weekly) for convenience',
                        'max_students': 'You can accept multiple students per availability block, but only one per consultation slot'
                    },
                    'consultation_requests': {
                        'title': 'Managing Consultation Requests',
                        'receiving': 'You\'ll receive email notifications instantly when a student requests a consultation',
                        'accepting': [
                            '1. Log into your dashboard',
                            '2. Go to "Pending Requests"',
                            '3. Review the student\'s details and requirements',
                            '4. Click "Accept" to confirm the consultation',
                            '5. Email confirmation sent to student',
                            '6. Both parties added to calendar'
                        ],
                        'declining': [
                            '1. Log into your dashboard',
                            '2. Go to "Pending Requests"',
                            '3. Click "Decline"',
                            '4. Optionally add decline reason',
                            '5. Email sent to student about decline',
                            '6. Student can request with another instructor'
                        ],
                        'reschedule': 'If you need to reschedule, use the consultation details page to update the time'
                    },
                    'consultation_conduct': {
                        'title': 'Conducting a Consultation',
                        'before': [
                            'Review student profile and consultation topic',
                            'Prepare relevant materials or resources',
                            'Join the video call 2-3 minutes early',
                            'Test audio/video quality'
                        ],
                        'during': [
                            'Greet the student professionally',
                            'Confirm the consultation topic and duration',
                            'Share screen if needed',
                            'Take notes for reference',
                            'Record session if allowed (with student consent)',
                            'Provide expert guidance and advice'
                        ],
                        'after': [
                            'Click "End Session" when complete',
                            'Optionally add notes to consultation record',
                            'Allow system to process feedback and completion'
                        ]
                    }
                },

                // ────────────────────────────────────────
                // ACCOUNT MANAGEMENT
                // ────────────────────────────────────────
                'account': {
                    'registration': {
                        'title': 'Creating Your Account',
                        'process': [
                            '1. Click "Sign Up" button',
                            '2. Enter your full name',
                            '3. Enter valid email address',
                            '4. Create strong password (min 8 characters)',
                            '5. Select your role (Student or Instructor)',
                            '6. Accept terms of service',
                            '7. Click "Create Account"',
                            '8. Verify your email (check your inbox)',
                            '9. Complete your profile'
                        ],
                        'email_verification': 'You\'ll receive a verification email. Click the link to confirm your address.',
                        'password_requirements': 'Password must be 8+ characters including uppercase, lowercase, number, and special character'
                    },
                    'login': {
                        'title': 'Logging In',
                        'steps': [
                            '1. Click "Login" button',
                            '2. Enter your email address',
                            '3. Enter your password',
                            '4. If MFA enabled, complete two-factor authentication',
                            '5. Click "Sign In"',
                            '6. You\'re now logged in to your dashboard'
                        ],
                        'forgot_password': 'Click "Forgot Password" on login page, enter email, and follow reset instructions sent to your inbox',
                        'mfa': 'Two-factor authentication adds extra security. Set it up in Account Settings for protection.'
                    },
                    'password_reset': {
                        'title': 'How to Reset Your Password',
                        'steps': [
                            '1. Go to login page',
                            '2. Click "Forgot Password?" link',
                            '3. Enter your registered email address',
                            '4. Click "Send Reset Link"',
                            '5. Check your email for password reset link (expires in 1 hour)',
                            '6. Click the link in the email',
                            '7. Enter your new password',
                            '8. Confirm the new password',
                            '9. Click "Reset Password"',
                            '10. Log in with your new password'
                        ],
                        'tips': 'Use a strong password different from your previous one. Never share your password with anyone.'
                    },
                    'profile': {
                        'title': 'Managing Your Profile',
                        'student_profile': 'Add a profile picture, bio, and academic information to help instructors understand your background',
                        'instructor_profile': 'Include your expertise, certifications, experience, and preferred consultation topics',
                        'privacy': 'Control who can see your contact information and consultation history in privacy settings',
                        'notifications': 'Customize email preferences for consultation updates, reminders, and platform announcements'
                    }
                },

                // ────────────────────────────────────────
                // NOTIFICATIONS & COMMUNICATION
                // ────────────────────────────────────────
                'notifications': {
                    'email_alerts': {
                        'title': 'Email Notifications',
                        'types': [
                            'Consultation Request Alert: Sent to instructor when student requests consultation',
                            'Request Approved: Sent to student when instructor accepts their request',
                            'Request Declined: Sent to student when instructor declines their request',
                            'Consultation Reminder: Sent 1 hour before scheduled consultation',
                            'Session Complete: Sent after consultation ends',
                            'Feedback Request: Sent to student to provide feedback',
                            'Password Reset: Sent when resetting account password',
                            'Login Alert: Sent on new device/location login'
                        ],
                        'customize': 'Manage notification preferences in Account Settings → Notifications',
                        'digest': 'Choose to receive daily or weekly digest instead of individual emails'
                    },
                    'in_app_alerts': {
                        'title': 'In-App Notifications',
                        'real_time': 'Important updates appear as notifications in your dashboard in real-time',
                        'bell_icon': 'Click the bell icon in navbar to see all notification history',
                        'dismiss': 'Mark notifications as read by clicking them'
                    }
                },

                // ────────────────────────────────────────
                // TECHNICAL INFORMATION
                // ────────────────────────────────────────
                'technical': {
                    'system_requirements': {
                        'browser': 'Modern browser: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+',
                        'internet': 'Minimum 2 Mbps upload/download for video. 4+ Mbps recommended for HD',
                        'hardware': 'Webcam, microphone, speaker, stable internet connection',
                        'os': 'Windows 10+, macOS 10.14+, Linux, iOS 14+, Android 8+'
                    },
                    'data_security': {
                        'title': 'How Your Data is Protected',
                        'encryption': 'All data transmitted using HTTPS encryption (TLS 1.2+)',
                        'storage': 'Personal data stored securely in encrypted database',
                        'webrtc': 'Video/audio encrypted end-to-end, not stored on servers',
                        'privacy': 'We never sell or share your data with third parties',
                        'compliance': 'System complies with data protection regulations'
                    },
                    'troubleshooting': {
                        'title': 'Common Issues & Solutions',
                        'page_not_loading': 'Try clearing browser cache or using incognito mode. Restart your browser.',
                        'slow_performance': 'Close other browser tabs/apps. Disable browser extensions. Check internet speed.',
                        'cannot_login': 'Verify email is correct. Reset password if needed. Check if account is active.',
                        'video_not_working': 'Allow camera permissions. Update browser. Restart computer. Check firewall settings.',
                        'audio_issues': 'Restart browser. Check microphone in system settings. Check browser audio permissions.',
                        'persistent_issues': 'Contact support team with: browser/OS info, error messages, and steps to reproduce'
                    }
                },

                // ────────────────────────────────────────
                // FEEDBACK & RATINGS
                // ────────────────────────────────────────
                'feedback': {
                    'title': 'Consultation Feedback',
                    'process': [
                        '1. After consultation ends, you\'ll see feedback form',
                        '2. Rate instructor/student on scale of 1-5 stars',
                        '3. Provide comments about the experience',
                        '4. Mention what went well and what could improve',
                        '5. Suggest topics for future consultations',
                        '6. Submit your feedback'
                    ],
                    'importance': 'Your feedback helps instructors improve and helps students choose better-matched consultants',
                    'anonymous': 'Feedback is processed to maintain professionalism'
                }
            }
        };
    }

    async sendMessage() {
        const text = this.input.value.trim();
        if (!text) return;

        // Add user message
        this.addMessage(text, 'user');
        this.input.value = '';

        // Hide quick actions after first message
        if (document.getElementById('chatbot-quick-actions')) {
            document.getElementById('chatbot-quick-actions').style.display = 'none';
        }

        // Show typing indicator
        this.showTypingIndicator();

        // Get AI response
        await this.simulateDelay(400);
        const response = this.generateResponse(text);
        this.removeTypingIndicator();
        this.addMessage(response, 'bot');
    }

    generateResponse(userInput) {
        const input = userInput.toLowerCase();
        let response = '';

        // ────────────────────────────────────────
        // CONSULTATION BOOKING
        // ────────────────────────────────────────
        if (this.matchesKeywords(input, ['book', 'consultation', 'schedule', 'request', 'appointment', 'reserve'])) {
            const steps = this.knowledgeBase.features.consultation.booking.steps;
            response = '<strong>Booking a Consultation</strong>\n\n';
            steps.forEach((step, i) => {
                response += (i > 0 ? '\n' : '') + step;
            });
            response += '\n\n<strong>Duration:</strong> ' + this.knowledgeBase.features.consultation.booking.duration;
            response += '\n\n<strong>Cancellation:</strong> ' + this.knowledgeBase.features.consultation.booking.cancellation;
        }

        // ────────────────────────────────────────
        // VIDEO CALL FEATURES
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['video', 'call', 'camera', 'audio', 'webrtc', 'peer', 'screen share'])) {
            const vc = this.knowledgeBase.features.consultation.video_call;
            response = '<strong>Video Call Information</strong>\n\n';
            response += '<strong>Technology:</strong> ' + vc.technology + '\n\n';
            response += '<strong>Requirements:</strong>\n';
            vc.requirements.forEach(req => {
                response += '\n• ' + req;
            });
            response += '\n\n<strong>Preparation Tips:</strong>\n';
            vc.preparation.forEach(tip => {
                response += '\n• ' + tip;
            });
        }

        // ────────────────────────────────────────
        // REGISTRATION & ACCOUNT SETUP
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['register', 'sign up', 'create account', 'new account', 'account', 'signup'])) {
            const steps = this.knowledgeBase.features.account.registration.process;
            response = '<strong>Creating Your Account</strong>\n\n';
            steps.forEach((step, i) => {
                response += (i > 0 ? '\n' : '') + step;
            });
            response += '\n\n<strong>Email Verification:</strong> ' + this.knowledgeBase.features.account.registration.email_verification;
        }

        // ────────────────────────────────────────
        // LOGIN & AUTHENTICATION
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['login', 'sign in', 'log in', 'cannot login', 'login problem', 'access', 'password mfa', 'two factor'])) {
            const login = this.knowledgeBase.features.account.login;
            response = '<strong>How to Login</strong>\n\n';
            login.steps.forEach((step, i) => {
                response += (i > 0 ? '\n' : '') + step;
            });
            response += '\n\n<strong>Forgot Password?</strong> ' + login.forgot_password;
        }

        // ────────────────────────────────────────
        // PASSWORD RESET
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['password', 'reset password', 'forgot password', 'change password', 'recover password', 'password reset'])) {
            const pr = this.knowledgeBase.features.account.password_reset;
            response = '<strong>Password Reset Instructions</strong>\n\n';
            pr.steps.forEach((step, i) => {
                response += (i > 0 ? '\n' : '') + step;
            });
            response += '\n\n<strong>Tips:</strong> ' + pr.tips;
        }

        // ────────────────────────────────────────
        // INSTRUCTOR FEATURES
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['instructor', 'availability', 'set availability', 'manage availability', 'accept consultation', 'decline request'])) {
            response = '<strong>Instructor Features</strong>\n\n';
            const instructor = this.knowledgeBase.roles.instructor;
            response += '<strong>Capabilities:</strong>\n';
            instructor.capabilities.forEach(cap => {
                response += '\n• ' + cap;
            });
            response += '\n\n<strong>Typical Workflow:</strong>\n' + instructor.workflow;
        }

        // ────────────────────────────────────────
        // SYSTEM FEATURES
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['features', 'what can', 'what features', 'capabilities', 'does system', 'functionality'])) {
            const features = this.knowledgeBase.system.features;
            response = '<strong>System Features</strong>\n\n';
            features.forEach((feature, i) => {
                response += (i > 0 ? '\n' : '') + '• ' + feature;
            });
        }

        // ────────────────────────────────────────
        // NOTIFICATIONS
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['notification', 'email', 'alert', 'reminder', 'message', 'notify'])) {
            const email = this.knowledgeBase.notifications.email_alerts;
            response = '<strong>Email Notifications</strong>\n\n';
            response += '<strong>Types of Notifications:</strong>\n';
            email.types.forEach(type => {
                response += '\n• ' + type;
            });
            response += '\n\n<strong>Customize:</strong> ' + email.customize;
        }

        // ────────────────────────────────────────
        // TECHNICAL ISSUES
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['troubleshoot', 'problem', 'issue', 'error', 'not working', 'bug', 'lag', 'slow', 'crash'])) {
            const troubleshooting = this.knowledgeBase.technical.troubleshooting;
            response = '<strong>Troubleshooting Guide</strong>\n\n';
            response += '• <strong>Page not loading:</strong> ' + troubleshooting.page_not_loading + '\n\n';
            response += '• <strong>Slow performance:</strong> ' + troubleshooting.slow_performance + '\n\n';
            response += '• <strong>Cannot login:</strong> ' + troubleshooting.cannot_login + '\n\n';
            response += '• <strong>Video not working:</strong> ' + troubleshooting.video_not_working + '\n\n';
            response += 'If issues persist, contact our support team with details.';
        }

        // ────────────────────────────────────────
        // FEEDBACK & RATINGS
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['feedback', 'rating', 'review', 'rate instructor', 'comment'])) {
            const feedback = this.knowledgeBase.feedback;
            response = '<strong>Providing Feedback</strong>\n\n';
            response += '<strong>Process:</strong>\n';
            feedback.process.forEach(step => {
                response += '\n' + step;
            });
            response += '\n\n<strong>Why It Matters:</strong> ' + feedback.importance;
        }

        // ────────────────────────────────────────
        // CANCELLATION
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['cancel', 'cancellation', 'decline', 'reschedule', 'refund'])) {
            const cancel = this.knowledgeBase.features.consultation.cancellation;
            response = '<strong>Cancellation & Rescheduling</strong>\n\n';
            response += '<strong>Student:</strong> ' + cancel.student + '\n\n';
            response += '<strong>Instructor:</strong> ' + cancel.instructor + '\n\n';
            response += '<strong>Refund Policy:</strong> ' + cancel.refund_policy + '\n\n';
            response += '<strong>Rescheduling:</strong> ' + cancel.rescheduling;
        }

        // ────────────────────────────────────────
        // SYSTEM SECURITY
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['security', 'privacy', 'encrypt', 'data', 'safe', 'protected', 'confidential'])) {
            const security = this.knowledgeBase.technical.data_security;
            response = '<strong>Data Security & Privacy</strong>\n\n';
            response += '<strong>Encryption:</strong> ' + security.encryption + '\n\n';
            response += '<strong>Storage:</strong> ' + security.storage + '\n\n';
            response += '<strong>Video Security:</strong> ' + security.webrtc + '\n\n';
            response += '<strong>Your Privacy:</strong> ' + security.privacy + '\n\n';
            response += '<strong>Compliance:</strong> ' + security.compliance;
        }

        // ────────────────────────────────────────
        // SYSTEM INFORMATION
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['about', 'system', 'platform', 'what is', 'technology', 'tech stack'])) {
            const system = this.knowledgeBase.system;
            response = '<strong>About ' + system.name + '</strong>\n\n';
            response += '<strong>Purpose:</strong> ' + system.purpose + '\n\n';
            response += '<strong>Key Features:</strong>\n';
            system.features.forEach(feature => {
                response += '\n• ' + feature;
            });
            response += '\n\n<strong>Technologies Used:</strong>\n';
            system.technologies.forEach(tech => {
                response += '\n• ' + tech;
            });
        }

        // ────────────────────────────────────────
        // STUDENT FEATURES
        // ────────────────────────────────────────
        else if (this.matchesKeywords(input, ['student', 'student feature', 'as student'])) {
            const student = this.knowledgeBase.roles.student;
            response = '<strong>Student Features & Capabilities</strong>\n\n';
            response += '<strong>What You Can Do:</strong>\n';
            student.capabilities.forEach(cap => {
                response += '\n• ' + cap;
            });
            response += '\n\n<strong>Your Journey:</strong>\n' + student.workflow;
        }

        // ────────────────────────────────────────
        // DEFAULT RESPONSE
        // ────────────────────────────────────────
        else {
            const questions = [
                'Could you rephrase that? Try asking about booking consultations, video calls, or account issues.',
                'I didn\'t quite understand. Would you like to know about:\n\n• How to book a consultation\n• Video call features\n• Password reset\n• System features\n• Instructor availability\n• Feedback process',
                'That\'s an interesting question! I specialize in this platform. Try asking about:\n\n• Student workflow\n• Instructor management\n• Technical troubleshooting\n• Account setup\n• Consultations'
            ];
            response = questions[Math.floor(Math.random() * questions.length)];
        }

        return response;
    }

    matchesKeywords(input, keywords) {
        return keywords.some(keyword => input.includes(keyword.toLowerCase()));
    }

    addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender === 'user' ? 'user-message' : 'bot-message'}`;

        if (sender === 'bot') {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                    </svg>
                </div>
                <div class="message-content">${text}</div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-content">${text}</div>
            `;
        }

        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot-message';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                </svg>
            </div>
            <div class="message-content">
                <div class="typing-indicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>
        `;
        this.messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }

    removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) indicator.remove();
    }

    scrollToBottom() {
        setTimeout(() => {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }, 0);
    }

    simulateDelay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chatbot = new SystemAIChatbot();
});
</script>
