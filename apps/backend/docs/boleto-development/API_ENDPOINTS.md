# Bole.to API Endpoints Documentation

## Authentication
All endpoints require authentication via `Authorization: Bearer {token}` header.

## Base URL
All endpoints are prefixed with the base API URL (typically `/api`).

---

## Social Feed Endpoints

### Get Event Feed
```http
GET /events/{event_id}/feed
```

**Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "content": "Great event!",
      "type": "text",
      "is_pinned": false,
      "is_organizer_post": false,
      "user": {...},
      "attendee": {...},
      "reactions_count": 5,
      "replies_count": 2
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45
  }
}
```

### Create Post
```http
POST /events/{event_id}/feed
```

**Body:**
```json
{
  "content": "Post content",
  "type": "text|image|announcement",
  "media_url": "https://example.com/image.jpg",
  "reply_to_id": 123
}
```

### Add Reaction
```http
POST /events/{event_id}/feed/{post_id}/reactions
```

**Body:**
```json
{
  "reaction_type": "like|fire|laugh|celebrate"
}
```

### Remove Reaction
```http
DELETE /events/{event_id}/feed/{post_id}/reactions
```

### Toggle Pin (Organizers Only)
```http
POST /events/{event_id}/feed/{post_id}/pin
```

---

## Disposable Camera Endpoints

### Get Event Photos
```http
GET /events/{event_id}/photos
```

**Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20)
- `revealed_only` (optional): Show only revealed photos (default: true)

### Upload Photo
```http
POST /events/{event_id}/photos
```

**Body:**
```json
{
  "photo_url": "https://example.com/photo.jpg",
  "thumbnail_url": "https://example.com/thumb.jpg",
  "blur_hash": "L6PZfSi_.AyE_3t7t7R**0o#DgR4",
  "taken_at": "2025-07-29T10:30:00Z"
}
```

### Add Photo Interaction
```http
POST /events/{event_id}/photos/{photo_id}/interactions
```

**Body (Reaction):**
```json
{
  "type": "reaction",
  "reaction_type": "like|fire|laugh|celebrate"
}
```

**Body (Comment):**
```json
{
  "type": "comment",
  "content": "Amazing photo!"
}
```

### Reveal Photo (Organizers Only)
```http
POST /events/{event_id}/photos/{photo_id}/reveal
```

### Get Photo Stats
```http
GET /events/{event_id}/photos/stats
```

**Response:**
```json
{
  "data": {
    "total_photos": 150,
    "revealed_photos": 120,
    "pending_photos": 30,
    "total_views": 2500,
    "reveal_percentage": 80.0
  }
}
```

---

## Poll Endpoints

### Get Event Polls
```http
GET /events/{event_id}/polls
```

**Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20)
- `open_only` (optional): Show only open polls (default: false)

### Create Poll (Organizers Only)
```http
POST /events/{event_id}/polls
```

**Body (Multiple Choice):**
```json
{
  "question": "What's your favorite music genre?",
  "type": "multiple_choice",
  "options": [
    {"text": "Rock", "color": "#FF5733"},
    {"text": "Pop", "color": "#33FF57"},
    {"text": "Electronic", "color": "#3357FF"}
  ],
  "allows_multiple": false,
  "is_anonymous": true,
  "closes_at": "2025-07-30T23:59:59Z"
}
```

### Submit Poll Response
```http
POST /events/{event_id}/polls/{poll_id}/responses
```

**Body:**
```json
{
  "selected_options": [1, 3]
}
```

### Get Poll Results
```http
GET /events/{event_id}/polls/{poll_id}/results
```

**Response:**
```json
{
  "data": {
    "poll": {...},
    "total_responses": 150,
    "response_stats": {
      "1": {"text": "Rock", "count": 75, "percentage": 50.0},
      "2": {"text": "Pop", "count": 45, "percentage": 30.0},
      "3": {"text": "Electronic", "count": 30, "percentage": 20.0}
    },
    "user_has_responded": true
  }
}
```

### Close Poll (Organizers Only)
```http
POST /events/{event_id}/polls/{poll_id}/close
```

---

## Analytics Endpoints

### Get Event Analytics
```http
GET /events/{event_id}/analytics
```

**Parameters:**
- `start_date` (optional): Start date (YYYY-MM-DD)
- `end_date` (optional): End date (YYYY-MM-DD)

**Response:**
```json
{
  "data": {
    "analytics": [...],
    "summary": {
      "total_posts": 245,
      "total_reactions": 1200,
      "total_photos": 180,
      "avg_daily_posts": 12.3
    },
    "trends": {
      "trend_percentage": 15.2,
      "trend_direction": "up"
    }
  }
}
```

### Get Real-time Metrics
```http
GET /events/{event_id}/analytics/realtime
```

**Response:**
```json
{
  "data": {
    "today": {
      "posts": 25,
      "reactions": 120,
      "photos": 18
    },
    "last_hour": {
      "posts": 3,
      "reactions": 15,
      "active_users": 8
    }
  }
}
```

### Get Engagement Leaderboard
```http
GET /events/{event_id}/analytics/leaderboard
```

**Parameters:**
- `days` (optional): Number of days to analyze (default: 7)

**Response:**
```json
{
  "data": [
    {
      "id": 123,
      "first_name": "John",
      "last_name": "Doe",
      "engagement_score": 45,
      "posts_count": 8,
      "reactions_count": 25,
      "photos_count": 12
    }
  ]
}
```

---

## Error Responses

All endpoints may return these error responses:

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "You must have a ticket to post in this event"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid",
  "errors": {
    "content": ["The content field is required."]
  }
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

---

## Authentication Requirements

- **Public Access**: Event feed viewing (when implemented)
- **Attendee Access**: Creating posts, reactions, uploading photos, poll responses
- **Organizer Access**: Pinning posts, creating polls, revealing photos, analytics
- **Account Admin**: Full access to all features

## Rate Limiting

API endpoints are rate-limited to prevent abuse:
- General endpoints: 60 requests per minute
- Upload endpoints: 10 requests per minute
- Analytics endpoints: 30 requests per minute