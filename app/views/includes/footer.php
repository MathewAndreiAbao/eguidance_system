    <?php
        $session = Registry::get_object('session');
        if (!$session) {
            $session = load_class('session', 'libraries');
        }
        $role = $session ? $session->userdata('role') : null;
        $name = '';
        if ($session) {
            $user_id = $session->userdata('user_id');
            // We would typically get the user's name from the profile, but for simplicity we'll use username
            $name = $session->userdata('username');
        }
    ?>
    

    
    <!-- Chatbot Icon - only appears for student roles -->
    <?php if($role == 'student'): ?>
        <!-- AI Chatbot -->
        <div id="ai-chatbot-container" class="ai-chatbot-wrapper">
            <div class="chatbot-header-bar">
                <button class="header-icon-btn" onclick="toggleChatbotMinimize()">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="header-title">
                    <div class="chatbot-logo">
                        <i class="fas fa-robot"></i>
                    </div>
                    <span class="chatbot-name">AI ChatBot</span>
                </div>
                <button class="header-icon-btn" onclick="toggleChatbotMinimize()">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div class="chatbot-messages" id="chatbot-messages">
                <div class="mb-3">
                    <div class="bg-blue-100 text-blue-800 rounded-lg p-3 inline-block max-w-xs">
                        Hello <?= htmlspecialchars($name) ?>! I'm your AI counseling assistant. How can I help you today?
                    </div>
                </div>
            </div>
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbot-input" placeholder="Type your message here..." onkeypress="handleKeyPress(event)">
                <button class="send-btn" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
        
        <div class="chatbot-popup-icon" onclick="toggleChatbot()" title="AI ChatBot">
            <i class="fas fa-robot"></i>
        </div>
        
        <script>
            function toggleChatbot() {
                const container = document.getElementById('ai-chatbot-container');
                if (container.style.display === 'none') {
                    // Show the chatbot and remove minimized class to show full conversation
                    container.style.display = 'flex';
                    container.classList.remove('chatbot-minimized');
                } else {
                    // Hide the chatbot
                    container.style.display = 'none';
                }
            }
            
            function toggleChatbotMinimize() {
                const container = document.getElementById('ai-chatbot-container');
                container.classList.toggle('chatbot-minimized');
            }
            
            function handleKeyPress(event) {
                if (event.key === 'Enter') {
                    sendMessage();
                }
            }
            
            async function sendMessage() {
                const input = document.getElementById('chatbot-input');
                const message = input.value.trim();
                
                if (message) {
                    // Add user message to chat
                    addMessageToChat(message, 'user');
                    input.value = '';
                    
                    // Show typing indicator
                    const typingIndicator = document.createElement('div');
                    typingIndicator.id = 'typing-indicator';
                    typingIndicator.className = 'mb-3';
                    typingIndicator.innerHTML = `
                        <div class="text-left">
                            <div class="bg-blue-100 text-blue-800 rounded-lg p-3 inline-block max-w-xs">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('chatbot-messages').appendChild(typingIndicator);
                    document.getElementById('chatbot-messages').scrollTop = document.getElementById('chatbot-messages').scrollHeight;
                    
                    try {
                        // Call the actual chatbot API
                        const response = await fetch('<?= site_url('chatbot/chat') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                message: message
                            })
                        });
                        
                        const data = await response.json();
                        
                        // Remove typing indicator
                        if (document.getElementById('typing-indicator')) {
                            document.getElementById('typing-indicator').remove();
                        }
                        
                        // Add AI response to chat
                        addMessageToChat(data.response, 'ai');
                    } catch (error) {
                        // Remove typing indicator
                        if (document.getElementById('typing-indicator')) {
                            document.getElementById('typing-indicator').remove();
                        }
                        
                        // Show error message
                        addMessageToChat('Sorry, I am currently unable to process your request. Please try again later.', 'ai');
                    }
                }
            }
            
            function addMessageToChat(message, sender) {
                const chatMessages = document.getElementById('chatbot-messages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'mb-3';
                
                if (sender === 'user') {
                    messageDiv.innerHTML = `
                        <div class="text-right">
                            <div class="bg-primary text-white rounded-lg p-3 inline-block max-w-xs">
                                ${message}
                            </div>
                        </div>
                    `;
                } else {
                    messageDiv.innerHTML = `
                        <div class="text-left">
                            <div class="bg-blue-100 text-blue-800 rounded-lg p-3 inline-block max-w-xs">
                                ${message}
                            </div>
                        </div>
                    `;
                }
                
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        </script>
    <?php endif; ?>
    
    <footer class="bg-white shadow mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © 2025 Eguidance. All rights reserved.
            </p>
        </div>
    </footer>
</div> <!-- Close main-content -->
</body>
</html>