# 💬 Sistema Chat One-to-One fra Utenti

> **Real-time chat con Pusher per utenti dello stesso tenant**  
> **Version:** 1.0 - Specification Document  
> **Data:** 23 Ottobre 2025

---

## 🎯 OBIETTIVO

Creare un sistema di **chat one-to-one** fra utenti PA registrati per:
- ✅ Scambio rapido di informazioni
- ✅ Condivisione analisi N.A.T.A.N.
- ✅ Collaborazione in tempo reale
- ✅ Isolamento per tenant (multi-tenancy ready)

---

## 📋 REQUIREMENTS

### **Functional Requirements**

| Feature | Priority | Description |
|---------|----------|-------------|
| **One-to-One Chat** | 🔴 MUST | Chat privata fra due utenti |
| **Tenant Isolation** | 🔴 MUST | Solo utenti stesso tenant |
| **Real-time** | 🔴 MUST | Pusher WebSocket |
| **NATAN Sharing** | 🟡 SHOULD | Condividi risposte N.A.T.A.N. |
| **Text Only** | 🔴 MUST | No file, no emoji, no immagini |
| **Typing Indicator** | 🟢 NICE | "X sta scrivendo..." |
| **Read Receipts** | 🟢 NICE | "Letto alle 10:30" |
| **Message History** | 🔴 MUST | Persistenza database |
| **Online Status** | 🟡 SHOULD | Verde/grigio indicator |
| **Search Users** | 🔴 MUST | Trova utenti per iniziare chat |

### **Non-Functional Requirements**

- ⚡ **Performance:** Messaggi consegnati in <500ms
- 🔒 **Security:** End-to-end encryption (opzionale fase 2)
- 📱 **Responsive:** Mobile-friendly UI
- ♿ **Accessibility:** WCAG 2.1 AA compliant
- 🌐 **Browser Support:** Chrome, Firefox, Safari, Edge (ultimi 2 versioni)

---

## 🏗️ ARCHITETTURA PROPOSTA

### **Tech Stack**

```
┌─────────────────────────────────────────────┐
│           Frontend (Blade + JS)             │
├─────────────────────────────────────────────┤
│ - Vanilla JavaScript (no framework)         │
│ - Pusher JS SDK                             │
│ - Tailwind CSS                              │
│ - SPA-like experience (no page reload)      │
└─────────────────────────────────────────────┘
                ↓
┌─────────────────────────────────────────────┐
│         Laravel Backend (PHP 8.2)           │
├─────────────────────────────────────────────┤
│ - ChatController (REST API)                 │
│ - ChatService (business logic)              │
│ - Pusher broadcast events                   │
│ - Authorization policies                    │
│ - Tenant scoping (global scope)             │
└─────────────────────────────────────────────┘
                ↓
┌─────────────────────────────────────────────┐
│            Pusher (WebSocket)               │
├─────────────────────────────────────────────┤
│ - Private channels per conversation         │
│ - Presence channels per user                │
│ - Event: MessageSent                        │
│ - Event: UserTyping                         │
│ - Event: MessageRead                        │
└─────────────────────────────────────────────┘
                ↓
┌─────────────────────────────────────────────┐
│          MariaDB Database                   │
├─────────────────────────────────────────────┤
│ - user_conversations                        │
│ - user_chat_messages                        │
│ - user_chat_participants                    │
└─────────────────────────────────────────────┘
```

---

## 💾 DATABASE SCHEMA

### **Table: user_conversations**

```sql
CREATE TABLE user_conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Tenant isolation
    tenant_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (tenant_id) REFERENCES pa_entities(id) ON DELETE CASCADE,
    INDEX idx_tenant (tenant_id),
    
    -- Conversation type (future: group chat)
    type ENUM('one_to_one', 'group') DEFAULT 'one_to_one',
    
    -- Metadata
    last_message_at TIMESTAMP NULL,
    last_message_preview TEXT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_last_message (last_message_at)
);
```

### **Table: user_chat_participants**

```sql
CREATE TABLE user_chat_participants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Relationships
    conversation_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (conversation_id) REFERENCES user_conversations(id) ON DELETE CASCADE,
    
    user_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Read tracking
    last_read_at TIMESTAMP NULL,
    unread_count INT UNSIGNED DEFAULT 0,
    
    -- Notifications
    is_muted BOOLEAN DEFAULT FALSE,
    
    -- Timestamps
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Constraints
    UNIQUE KEY unique_conversation_user (conversation_id, user_id),
    INDEX idx_user_conversations (user_id, conversation_id)
);
```

### **Table: user_chat_messages**

```sql
CREATE TABLE user_chat_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Relationships
    conversation_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (conversation_id) REFERENCES user_conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    
    sender_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    
    -- Content
    message_type ENUM('text', 'natan_share') DEFAULT 'text',
    content TEXT NOT NULL,
    
    -- For NATAN shares
    natan_message_id BIGINT UNSIGNED NULL,
    FOREIGN KEY (natan_message_id) REFERENCES natan_chat_messages(id) ON DELETE SET NULL,
    INDEX idx_natan (natan_message_id),
    
    -- Read tracking
    read_by JSON NULL, -- [{user_id: 2, read_at: "2025-10-23T10:30:00Z"}]
    
    -- Soft delete (future: message deletion)
    deleted_at TIMESTAMP NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_created (created_at)
);
```

---

## 🎨 UI/UX DESIGN

### **Layout: Chat Interface**

```
┌────────────────────────────────────────────────────────────┐
│  Header: EGI Platform                           [User ▼]   │
├──────────────────┬─────────────────────────────────────────┤
│                  │  Chat con: Mario Rossi (Comune Firenze) │
│  Conversazioni   │  🟢 Online                               │
│                  ├─────────────────────────────────────────┤
│  🔍 [Search...]  │                                          │
│                  │  [Chat messages area]                    │
│  ─────────────   │                                          │
│                  │  ┌──────────────────────────────────┐   │
│  Mario Rossi     │  │ Messaggio da te                   │   │
│  🟢 Comune FI    │  │ 10:30 AM ✓✓                      │   │
│  "Ciao, ho..."   │  └──────────────────────────────────┘   │
│  3 min fa   (2)  │                                          │
│                  │  ┌──────────────────────────────────┐   │
│  ─────────────   │  │ Risposta da Mario                 │   │
│                  │  │ 10:31 AM                         │   │
│  Anna Bianchi    │  └──────────────────────────────────┘   │
│  ⚫ Comune FI    │                                          │
│  "Grazie per..." │  📊 [Analisi N.A.T.A.N. condivisa]      │
│  1h fa           │  "Strategie per mobilità..."             │
│                  │  [Vedi analisi completa]                 │
│  ─────────────   │                                          │
│                  │  Mario sta scrivendo...                  │
│  + Nuova Chat    ├─────────────────────────────────────────┤
│                  │  [Scrivi un messaggio...]          [⬆]  │
└──────────────────┴─────────────────────────────────────────┘
```

### **Colors & Branding**

```css
/* Chat bubbles */
.message-sent {
    background: #2D5016; /* EGI green */
    color: white;
    align-self: flex-end;
}

.message-received {
    background: #f3f4f6; /* Gray-100 */
    color: #1f2937; /* Gray-800 */
    align-self: flex-start;
}

/* NATAN share card */
.natan-share {
    border-left: 4px solid #2D5016;
    background: #f0fdf4; /* Green-50 */
    padding: 12px;
    border-radius: 8px;
}

/* Online status */
.online { color: #10b981; } /* Green-500 */
.offline { color: #6b7280; } /* Gray-500 */
```

---

## 🔌 API ENDPOINTS

### **Base URL:** `/api/user-chat`

#### **1. GET `/conversations`** - Lista conversazioni utente

**Response:**
```json
{
    "success": true,
    "conversations": [
        {
            "id": 1,
            "type": "one_to_one",
            "participant": {
                "id": 5,
                "name": "Mario Rossi",
                "avatar": null,
                "is_online": true,
                "entity_name": "Comune di Firenze"
            },
            "last_message": {
                "preview": "Ciao, ho visto l'analisi...",
                "sent_at": "2025-10-23T10:30:00Z",
                "is_read": true
            },
            "unread_count": 2
        }
    ]
}
```

---

#### **2. POST `/conversations`** - Crea/trova conversazione

**Request:**
```json
{
    "recipient_id": 5
}
```

**Response:**
```json
{
    "success": true,
    "conversation_id": 1,
    "is_new": false
}
```

**Business Logic:**
- Se esiste conversazione one-to-one fra i due utenti → return existing
- Altrimenti → create new
- Verifica tenant isolation (stesso tenant)

---

#### **3. GET `/conversations/{id}/messages`** - Carica messaggi

**Query Params:**
- `limit` (default: 50)
- `before` (message_id, per pagination)

**Response:**
```json
{
    "success": true,
    "messages": [
        {
            "id": 123,
            "sender_id": 2,
            "sender_name": "Tu",
            "message_type": "text",
            "content": "Ciao, hai visto l'ultimo atto?",
            "created_at": "2025-10-23T10:30:00Z",
            "is_read": true,
            "read_at": "2025-10-23T10:31:00Z"
        },
        {
            "id": 124,
            "sender_id": 5,
            "sender_name": "Mario Rossi",
            "message_type": "natan_share",
            "content": "Guarda questa analisi strategica",
            "natan_data": {
                "message_id": 456,
                "persona": "Consulente Strategico",
                "preview": "Strategie per migliorare la mobilità...",
                "sources_count": 12
            },
            "created_at": "2025-10-23T10:31:00Z"
        }
    ],
    "has_more": true
}
```

---

#### **4. POST `/conversations/{id}/messages`** - Invia messaggio

**Request (Text Message):**
```json
{
    "message_type": "text",
    "content": "Ciao, come stai?"
}
```

**Request (NATAN Share):**
```json
{
    "message_type": "natan_share",
    "content": "Guarda questa analisi",
    "natan_message_id": 456
}
```

**Response:**
```json
{
    "success": true,
    "message": {
        "id": 125,
        "content": "Ciao, come stai?",
        "created_at": "2025-10-23T10:32:00Z"
    }
}
```

**Backend Actions:**
1. Validate conversation access (user is participant)
2. Save message to database
3. **Broadcast Pusher event** → `private-conversation.{id}`
4. Update conversation `last_message_at`
5. Increment `unread_count` for recipient
6. Return message

---

#### **5. POST `/conversations/{id}/typing`** - Typing indicator

**Request:**
```json
{
    "is_typing": true
}
```

**Backend Actions:**
- Broadcast Pusher event → `private-conversation.{id}`
- Event: `UserTyping` { user_id, name, is_typing }
- No database save (ephemeral)

---

#### **6. POST `/messages/{id}/read`** - Segna messaggio come letto

**Backend Actions:**
1. Update `read_by` JSON in message
2. Update participant `last_read_at`
3. Decrement `unread_count`
4. Broadcast Pusher event → `MessageRead`

---

#### **7. GET `/users/search`** - Cerca utenti per iniziare chat

**Query Params:**
- `q` (search term)

**Response:**
```json
{
    "success": true,
    "users": [
        {
            "id": 5,
            "name": "Mario Rossi",
            "email": "mario.rossi@comune.firenze.it",
            "entity_name": "Comune di Firenze",
            "is_online": true
        }
    ]
}
```

**Filters:**
- Same tenant only
- Exclude current user
- Match: name, email

---

## 🔔 PUSHER EVENTS

### **Setup Pusher**

```bash
composer require pusher/pusher-php-server
npm install --save pusher-js
```

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=eu
```

```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],
```

---

### **Event: MessageSent**

**Channel:** `private-conversation.{conversation_id}`

```php
// app/Events/MessageSent.php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    public $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }
    
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'message_type' => $this->message->message_type,
            'natan_data' => $this->message->natan_message_id 
                ? $this->loadNatanData() 
                : null,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
```

**Dispatch:**
```php
// ChatService.php
event(new MessageSent($message));
```

---

### **Event: UserTyping**

**Channel:** `private-conversation.{conversation_id}`

```php
// app/Events/UserTyping.php
class UserTyping implements ShouldBroadcast
{
    public $conversationId;
    public $userId;
    public $userName;
    public $isTyping;
    
    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->conversationId);
    }
}
```

**Frontend (debounced):**
```javascript
let typingTimeout;
userInput.addEventListener('input', () => {
    clearTimeout(typingTimeout);
    
    // Send "is typing"
    fetch(`/api/user-chat/conversations/${conversationId}/typing`, {
        method: 'POST',
        body: JSON.stringify({ is_typing: true })
    });
    
    // Stop typing after 3s
    typingTimeout = setTimeout(() => {
        fetch(`/api/user-chat/conversations/${conversationId}/typing`, {
            method: 'POST',
            body: JSON.stringify({ is_typing: false })
        });
    }, 3000);
});
```

---

### **Event: MessageRead**

**Channel:** `private-conversation.{conversation_id}`

```php
class MessageRead implements ShouldBroadcast
{
    public $messageId;
    public $userId;
    public $readAt;
}
```

**Frontend (Intersection Observer):**
```javascript
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const messageId = entry.target.dataset.messageId;
            markAsRead(messageId);
        }
    });
});

// Observe all unread messages
document.querySelectorAll('.message[data-unread="true"]').forEach(el => {
    observer.observe(el);
});
```

---

## 🔐 AUTHORIZATION & SECURITY

### **Policy: ChatPolicy**

```php
// app/Policies/ChatPolicy.php
namespace App\Policies;

class ChatPolicy
{
    /**
     * Can user access this conversation?
     */
    public function view(User $user, UserConversation $conversation)
    {
        // User must be participant
        return $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
    }
    
    /**
     * Can user send message in this conversation?
     */
    public function sendMessage(User $user, UserConversation $conversation)
    {
        return $this->view($user, $conversation);
    }
    
    /**
     * Can user start conversation with recipient?
     */
    public function startConversation(User $user, User $recipient)
    {
        // Must be in same tenant
        return $user->tenant_id === $recipient->tenant_id;
    }
}
```

### **Pusher Channel Authorization**

```php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Verify user is participant
    return \App\Models\UserChatParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->exists();
});
```

**Frontend:**
```javascript
const pusher = new Pusher(PUSHER_KEY, {
    cluster: 'eu',
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    }
});

const channel = pusher.subscribe(`private-conversation.${conversationId}`);
channel.bind('MessageSent', (data) => {
    appendMessage(data);
});
```

---

## 🎯 NATAN SHARING FEATURE

### **User Flow:**

1. User vede risposta N.A.T.A.N. interessante
2. Click button **"Condividi in chat"** → Mostra lista conversazioni
3. Seleziona destinatario
4. Messaggio inviato con tipo `natan_share`

### **UI: Share Button in N.A.T.A.N. Chat**

```html
<!-- In ogni messaggio N.A.T.A.N. -->
<div class="message-actions">
    <button class="copy-message-btn">
        <span class="material-icons">content_copy</span> Copia
    </button>
    <button class="share-natan-btn" data-message-id="456">
        <span class="material-icons">share</span> Condividi in chat
    </button>
</div>
```

### **UI: Share Modal**

```html
<div id="shareModal" class="modal hidden">
    <div class="modal-content">
        <h3>Condividi analisi N.A.T.A.N.</h3>
        
        <div class="preview">
            <strong>🎯 Consulente Strategico</strong>
            <p>Strategie per migliorare la mobilità urbana...</p>
            <small>12 fonti | 23 Ott 2025</small>
        </div>
        
        <h4>Seleziona destinatario:</h4>
        <ul class="conversation-list">
            <li data-conversation-id="1">
                <span>Mario Rossi</span>
                <small>Comune di Firenze</small>
            </li>
            <li data-conversation-id="2">
                <span>Anna Bianchi</span>
                <small>Comune di Firenze</small>
            </li>
        </ul>
        
        <button class="btn-cancel">Annulla</button>
    </div>
</div>
```

### **Backend: Store NATAN Share**

```php
// ChatService.php
public function shareNatanMessage(
    int $conversationId, 
    int $natanMessageId, 
    User $sender,
    ?string $personalNote = null
): UserChatMessage
{
    // Load N.A.T.A.N. message
    $natanMessage = NatanChatMessage::findOrFail($natanMessageId);
    
    // Verify sender has access to this N.A.T.A.N. message
    if ($natanMessage->user_id !== $sender->id) {
        throw new UnauthorizedException('Cannot share others\' N.A.T.A.N. analyses');
    }
    
    // Create chat message
    $message = UserChatMessage::create([
        'conversation_id' => $conversationId,
        'sender_id' => $sender->id,
        'message_type' => 'natan_share',
        'content' => $personalNote ?? 'Ha condiviso un\'analisi N.A.T.A.N.',
        'natan_message_id' => $natanMessageId,
    ]);
    
    // Broadcast
    event(new MessageSent($message));
    
    return $message;
}
```

### **Frontend: Render NATAN Share**

```javascript
function renderNatanShare(message) {
    const natanData = message.natan_data;
    
    return `
        <div class="message ${message.sender_id === currentUserId ? 'sent' : 'received'}">
            <p class="message-text">${escapeHtml(message.content)}</p>
            
            <div class="natan-share-card">
                <div class="natan-header">
                    <span class="persona-icon">${natanData.persona_icon}</span>
                    <strong>${natanData.persona_name}</strong>
                </div>
                
                <p class="natan-preview">
                    ${escapeHtml(natanData.content_preview)}
                </p>
                
                <div class="natan-meta">
                    <span>📚 ${natanData.sources_count} fonti</span>
                    <span>•</span>
                    <span>${formatDate(natanData.created_at)}</span>
                </div>
                
                <a href="/pa/natan/chat?message=${natanData.message_id}" 
                   class="view-full-analysis" target="_blank">
                    Vedi analisi completa →
                </a>
            </div>
            
            <span class="message-time">${formatTime(message.created_at)}</span>
        </div>
    `;
}
```

---

## 📊 ANALYTICS & MONITORING

### **Metrics to Track**

```php
// ChatAnalyticsService.php

// Daily Active Users (DAU)
public function getDailyActiveUsers(int $tenantId, Carbon $date): int
{
    return UserChatMessage::where('created_at', '>=', $date->startOfDay())
        ->where('created_at', '<=', $date->endOfDay())
        ->whereHas('conversation', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })
        ->distinct('sender_id')
        ->count();
}

// Average response time
public function getAverageResponseTime(int $conversationId): float
{
    $messages = UserChatMessage::where('conversation_id', $conversationId)
        ->orderBy('created_at')
        ->get();
    
    $responseTimes = [];
    for ($i = 1; $i < count($messages); $i++) {
        if ($messages[$i]->sender_id !== $messages[$i-1]->sender_id) {
            $diff = $messages[$i]->created_at->diffInSeconds($messages[$i-1]->created_at);
            $responseTimes[] = $diff;
        }
    }
    
    return count($responseTimes) > 0 ? array_sum($responseTimes) / count($responseTimes) : 0;
}

// Most shared N.A.T.A.N. personas
public function getMostSharedPersonas(int $tenantId): array
{
    return UserChatMessage::where('message_type', 'natan_share')
        ->whereHas('conversation', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })
        ->join('natan_chat_messages', 'user_chat_messages.natan_message_id', '=', 'natan_chat_messages.id')
        ->select('natan_chat_messages.persona_name', DB::raw('COUNT(*) as share_count'))
        ->groupBy('natan_chat_messages.persona_name')
        ->orderByDesc('share_count')
        ->get();
}
```

---

## 🚀 IMPLEMENTATION ROADMAP

### **Phase 1: MVP (1 settimana)**

- [ ] Database migrations (3 tables)
- [ ] Models + relationships
- [ ] Basic API endpoints (create conversation, send message, load messages)
- [ ] Pusher setup + MessageSent event
- [ ] Basic UI (conversation list + chat window)
- [ ] Text-only messages
- [ ] Tenant isolation (global scope)

**Deliverable:** Funzionante chat one-to-one con real-time

---

### **Phase 2: N.A.T.A.N. Sharing (3 giorni)**

- [ ] Share button in N.A.T.A.N. chat
- [ ] Share modal UI
- [ ] `natan_share` message type
- [ ] Render N.A.T.A.N. card in chat
- [ ] Link to full analysis

**Deliverable:** Condivisione analisi N.A.T.A.N. fra utenti

---

### **Phase 3: Enhanced UX (3 giorni)**

- [ ] Typing indicator (UserTyping event)
- [ ] Read receipts (MessageRead event + Intersection Observer)
- [ ] Online status (Pusher presence channel)
- [ ] Unread count badges
- [ ] User search autocomplete
- [ ] Mobile responsive layout

**Deliverable:** UX professionale livello WhatsApp Web

---

### **Phase 4: Polish & Testing (2 giorni)**

- [ ] Unit tests (ChatService)
- [ ] Feature tests (API endpoints)
- [ ] Browser tests (Dusk, chat flow)
- [ ] Performance testing (100 concurrent users)
- [ ] Security audit (XSS, CSRF, authorization)
- [ ] Documentation

**Deliverable:** Production-ready

---

### **Total Effort:** ~2 settimane (1 sviluppatore full-time)

---

## 🔮 FUTURE ENHANCEMENTS (Phase 5+)

### **Advanced Features**

- [ ] **Group Chats:** 3+ partecipanti, admin roles
- [ ] **File Attachments:** PDF, immagini (max 10MB)
- [ ] **Voice Messages:** Audio recording + playback
- [ ] **Message Reactions:** 👍 👎 ❤️ (emoji limitate)
- [ ] **Message Threading:** Reply to specific message
- [ ] **Search in Chat:** Full-text search messaggi
- [ ] **Archive Conversations:** Hide without delete
- [ ] **Export Chat:** Download conversazione in PDF/TXT
- [ ] **Desktop Notifications:** Browser push notifications
- [ ] **End-to-End Encryption:** Libreria openpgp.js

### **Admin Features**

- [ ] **Moderation Dashboard:** View all tenant chats
- [ ] **Analytics Dashboard:** Usage metrics, graphs
- [ ] **Abuse Reporting:** Segnala messaggi inappropriati
- [ ] **Auto-moderation:** AI-based content filtering

---

## 💰 COST ESTIMATION

### **Pusher Pricing**

**Free Tier:**
- 200k messages/day
- 100 concurrent connections
- Unlimited channels

**Pro Plan ($49/month):**
- 1M messages/day
- Unlimited connections
- Priority support

**Stima per 50 utenti attivi:**
- ~5k messages/day
- ~20 concurrent connections
- ✅ **Free tier sufficiente per 6-12 mesi**

### **Development Cost**

- **Phase 1-4:** ~2 settimane × €500/day = **€5,000**
- **Maintenance:** ~4h/mese × €60/h = **€240/mese**

---

## 📚 REFERENCES

- **Pusher Docs:** https://pusher.com/docs
- **Laravel Broadcasting:** https://laravel.com/docs/broadcasting
- **pusher-js SDK:** https://github.com/pusher/pusher-js
- **WhatsApp Web UX Patterns:** Inspiration per UI/UX

---

**Documentato da:** AI Agent (Claude Sonnet 3.5)  
**Ultima revisione:** 23 Ottobre 2025  
**Status:** Specification (not yet implemented)

