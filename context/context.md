# Bole.to Platform Development Context

## Project Overview

**Bole.to** is a next-generation event ticketing platform that revolutionizes the event experience by adding a social layer inspired by the old Facebook Events. Built on top of the open-source Hi.Events platform, Bole.to transforms every event into a "flight" - where buying a ticket is just the beginning of the journey.

### Vision Statement
"We don't just sell tickets, we create communities. From the moment you book your 'flight' until long after you've 'landed,' Bole.to keeps everyone connected in the same interactive lounge."

### Core Differentiator
While competitors focus solely on ticket sales, Bole.to creates anticipation, engagement, and lasting memories through integrated social features that span before, during, and after events.

## Target Market

### Primary Market
- **Geography**: Starting in Nicaragua, expanding to Central America, then Latin America
- **Demographics**: 18-35 year-olds who are social media native
- **Event Types**: Concerts, clubs, festivals, conferences, sports events

### User Personas
1. **Event Attendees**: Want to connect with others, share experiences, and relive memories
2. **Event Organizers**: Need tools to build hype, communicate efficiently, and understand their audience
3. **Promoters**: Require analytics and engagement tools to maximize ticket sales

## Core Features

### 1. Social Feed (The "Boarding Lounge")
- **Timeline**: Before, during, and after events
- **Capabilities**:
  - Text posts, images, GIFs, memes
  - Emoji reactions (👍 🔥 😂 🎉)
  - Nested comments
  - Pinned announcements by organizers
  - Real-time updates via WebSockets
- **Access Control**: Only ticket holders can participate

### 2. Interactive Polls
- **Creator**: Event organizers
- **Types**: Multiple choice, rating scales, open-ended
- **Use Cases**: Song requests, venue feedback, future event planning
- **Results**: Real-time, visible to all attendees

### 3. Disposable Camera Feature
- **Concept**: Limited shots per attendee during event (e.g., 10-15 photos)
- **Capture**: Through in-app camera only
- **Storage**: Photos uploaded immediately but hidden
- **Reveal**: Batch reveal next morning (e.g., 9 AM)
- **Interaction**: Shared gallery with comments and reactions

### 4. Airline-Themed UX
- **Booking**: "Book your flight" instead of "Buy ticket"
- **Event Page**: "Flight details" with departure/arrival times
- **Check-in**: "Boarding pass" QR codes
- **Organizers**: "Pilots" or "Crew"
- **Attendees**: "Passengers"

### 5. Enhanced Organizer Tools
- **Live Updates**: Push notifications to all attendees
- **Moderation**: Remove posts, mute users
- **Analytics**: Engagement metrics, sentiment analysis
- **Communication**: Direct messaging to ticket segments

## Technical Architecture

### Base Platform: Hi.Events
- **Version**: Latest stable
- **Core Features Used**:
  - Multi-tenant architecture
  - Event management system
  - Ticketing and payment (Stripe Connect)
  - Check-in system
  - Email notifications
  - Admin panel

### Backend Architecture

```
Technology Stack:
- Framework: Laravel 11.x (from Hi.Events)
- Language: PHP 8.3+
- Database: PostgreSQL 15+
- Cache/Queue: Redis 7.x
- Real-time: Pusher or Soketi (self-hosted Pusher)
- File Storage: S3-compatible (AWS S3 or Cloudflare R2)
- Search: PostgreSQL full-text search (upgrade to Elasticsearch if needed)
```

### Frontend Architecture

```
Web Application:
- Framework: Next.js 14 (App Router)
- Language: TypeScript 5.x
- UI Library: Mantine 7.x (consistency with Hi.Events)
- State Management: TanStack Query + Zustand
- Real-time: Pusher JS client
- Styling: TailwindCSS
- Build Tool: Turbo (monorepo management)

Mobile Application:
- Framework: React Native + Expo SDK 50
- Language: TypeScript 5.x
- Navigation: React Navigation 6
- UI Components: Tamagui or React Native Elements or tailwindcss
- State Management: TanStack Query + Zustand (shared with web)
- Camera: expo-camera
- Notifications: expo-notifications
- Storage: AsyncStorage + MMKV
```

### Shared Code Architecture

```
Monorepo Structure:
boleto/
├── apps/
│   ├── backend/          # Extended Hi.Events Laravel API
│   ├── web/             # Next.js web application
│   └── mobile/          # React Native application
├── packages/
│   ├── api-client/      # Shared API client (TypeScript)
│   ├── types/           # Shared TypeScript interfaces
│   ├── utils/           # Shared utilities
│   └── ui/              # Shared React components (web-only)
├── infrastructure/      # Docker, deployment configs
└── docs/               # Documentation
```

## Database Schema Extensions

### New Tables for Social Features

```sql
-- Event social feed posts
CREATE TABLE event_posts (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    attendee_id BIGINT REFERENCES attendees(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'text', -- text, image, poll, announcement
    media_url TEXT,
    is_pinned BOOLEAN DEFAULT FALSE,
    is_organizer_post BOOLEAN DEFAULT FALSE,
    reply_to_id BIGINT REFERENCES event_posts(id) ON DELETE CASCADE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_event_posts_event_id (event_id),
    INDEX idx_event_posts_created_at (created_at)
);

-- Reactions to posts
CREATE TABLE post_reactions (
    id BIGSERIAL PRIMARY KEY,
    post_id BIGINT NOT NULL REFERENCES event_posts(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    attendee_id BIGINT REFERENCES attendees(id) ON DELETE CASCADE,
    reaction_type VARCHAR(20) NOT NULL, -- like, fire, laugh, celebrate
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_post_reaction (post_id, user_id),
    INDEX idx_reactions_post_id (post_id)
);

-- Disposable camera photos
CREATE TABLE event_photos (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    attendee_id BIGINT NOT NULL REFERENCES attendees(id) ON DELETE CASCADE,
    photo_url TEXT NOT NULL,
    thumbnail_url TEXT,
    blur_hash TEXT, -- For placeholder while loading
    taken_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reveal_at TIMESTAMP NOT NULL,
    is_revealed BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_photos_event_reveal (event_id, reveal_at),
    INDEX idx_photos_attendee (attendee_id)
);

-- Photo reactions and comments
CREATE TABLE photo_interactions (
    id BIGSERIAL PRIMARY KEY,
    photo_id BIGINT NOT NULL REFERENCES event_photos(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(20) NOT NULL, -- reaction, comment
    content TEXT, -- For comments
    reaction_type VARCHAR(20), -- For reactions
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_photo_interactions_photo (photo_id)
);

-- Event polls
CREATE TABLE event_polls (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    created_by BIGINT NOT NULL REFERENCES users(id),
    question TEXT NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'multiple_choice', -- multiple_choice, rating, open_ended
    options JSONB, -- Array of {id, text, color} for choices
    allows_multiple BOOLEAN DEFAULT FALSE,
    is_anonymous BOOLEAN DEFAULT TRUE,
    closes_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_polls_event (event_id)
);

-- Poll responses
CREATE TABLE poll_responses (
    id BIGSERIAL PRIMARY KEY,
    poll_id BIGINT NOT NULL REFERENCES event_polls(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    attendee_id BIGINT REFERENCES attendees(id) ON DELETE CASCADE,
    selected_options JSONB, -- Array of option IDs or values
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_poll_response (poll_id, user_id),
    INDEX idx_poll_responses_poll (poll_id)
);

-- Push notification tokens
CREATE TABLE user_push_tokens (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token TEXT NOT NULL,
    platform VARCHAR(20) NOT NULL, -- ios, android, web
    device_id TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_token (user_id, token),
    INDEX idx_push_tokens_user (user_id)
);

-- Event engagement analytics
CREATE TABLE event_engagement_analytics (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    total_posts INT DEFAULT 0,
    total_reactions INT DEFAULT 0,
    total_photos INT DEFAULT 0,
    unique_engaged_users INT DEFAULT 0,
    poll_participation_rate DECIMAL(5,2),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_event_date_analytics (event_id, date),
    INDEX idx_analytics_event (event_id)
);
```

## API Endpoints Extension

### Social Feed Endpoints
```
GET    /api/events/{eventId}/feed?page=1&limit=20
POST   /api/events/{eventId}/posts
GET    /api/events/{eventId}/posts/{postId}
PUT    /api/events/{eventId}/posts/{postId}
DELETE /api/events/{eventId}/posts/{postId}
POST   /api/events/{eventId}/posts/{postId}/reactions
DELETE /api/events/{eventId}/posts/{postId}/reactions
POST   /api/events/{eventId}/posts/{postId}/pin
DELETE /api/events/{eventId}/posts/{postId}/pin
```

### Disposable Camera Endpoints
```
POST   /api/events/{eventId}/photos/capture
GET    /api/events/{eventId}/photos/remaining
GET    /api/events/{eventId}/photos/gallery?revealed=true
GET    /api/events/{eventId}/photos/{photoId}
POST   /api/events/{eventId}/photos/{photoId}/reactions
POST   /api/events/{eventId}/photos/{photoId}/comments
DELETE /api/events/{eventId}/photos/{photoId}
```

### Polls Endpoints
```
GET    /api/events/{eventId}/polls
POST   /api/events/{eventId}/polls
GET    /api/events/{eventId}/polls/{pollId}
PUT    /api/events/{eventId}/polls/{pollId}
DELETE /api/events/{eventId}/polls/{pollId}
POST   /api/events/{eventId}/polls/{pollId}/responses
GET    /api/events/{eventId}/polls/{pollId}/results
```

### Push Notifications Endpoints
```
POST   /api/users/push-tokens
DELETE /api/users/push-tokens/{token}
POST   /api/events/{eventId}/notifications/broadcast
```

### Analytics Endpoints (Organizer Only)
```
GET    /api/events/{eventId}/analytics/engagement
GET    /api/events/{eventId}/analytics/social
GET    /api/events/{eventId}/analytics/photos
GET    /api/events/{eventId}/analytics/polls
```

## Real-time Events (WebSocket/Pusher)

### Channel Structure
```
// Public event channel
event.{eventId}
  - post.created
  - post.updated
  - post.deleted
  - reaction.added
  - reaction.removed
  - poll.created
  - poll.closed
  - announcement.created

// Private user channel
private-user.{userId}
  - notification.received
  - photo.revealed
  - message.received

// Presence channel for live attendee count
presence-event.{eventId}
  - member_added
  - member_removed
```

## Development Timeline

### Phase 1: Foundation (Weeks 1-4)
**Sprint 1 (Week 1-2): Environment Setup & Planning**
- Set up development environment
- Fork and configure Hi.Events
- Set up monorepo structure
- Configure Docker environments
- Create detailed technical specifications
- Design database schema
- Set up CI/CD pipeline

**Sprint 2 (Week 3-4): Backend Core Extensions**
- Implement database migrations for social features
- Create base models (EventPost, EventPhoto, Poll, etc.)
- Set up real-time broadcasting with Pusher/Soketi
- Implement authentication extensions for mobile
- Create base API endpoints for social features
- Set up file upload service for photos
- Implement push notification service

### Phase 2: Social Features Backend (Weeks 5-8)
**Sprint 3 (Week 5-6): Social Feed Implementation**
- Complete social feed CRUD operations
- Implement reaction system
- Add comment threading
- Create moderation tools
- Implement real-time updates
- Add feed pagination and filtering
- Create organizer announcement system

**Sprint 4 (Week 7-8): Camera & Polls**
- Implement disposable camera backend
- Create photo storage and CDN integration
- Build reveal scheduling system
- Implement poll creation and voting
- Add real-time poll results
- Create photo gallery with interactions
- Add engagement analytics collection

### Phase 3: Web Application (Weeks 9-12)
**Sprint 5 (Week 9-10): Web App Foundation**
- Set up Next.js project with TypeScript
- Implement authentication flow
- Create shared API client
- Build event discovery pages
- Implement ticket purchase flow
- Add user profile management
- Set up real-time connections

**Sprint 6 (Week 11-12): Web Social Features**
- Build social feed component
- Implement real-time updates
- Create poll participation UI
- Add photo gallery viewer
- Build organizer dashboard
- Implement moderation interface
- Add analytics visualization

### Phase 4: Mobile Application (Weeks 13-18)
**Sprint 7 (Week 13-14): Mobile Foundation**
- Set up React Native/Expo project
- Implement navigation structure
- Create authentication flow
- Build event discovery screens
- Implement ticket wallet
- Add push notification setup
- Create offline storage layer

**Sprint 8 (Week 15-16): Mobile Social Features**
- Build social feed with pull-to-refresh
- Implement camera interface
- Create photo capture flow
- Add poll participation
- Build photo gallery with reveal animation
- Implement real-time updates
- Add offline queue for posts/photos

**Sprint 9 (Week 17-18): Mobile Polish & Check-in**
- Implement QR code scanner
- Build offline check-in capability
- Add attendee management
- Create event staff mode
- Polish UI/UX with animations
- Implement deep linking
- Add analytics tracking

### Phase 5: Integration & Testing (Weeks 19-22)
**Sprint 10 (Week 19-20): Integration**
- End-to-end testing across platforms
- Performance optimization
- Security audit
- Load testing for real-time features
- CDN and caching optimization
- API rate limiting
- Error tracking setup

**Sprint 11 (Week 21-22): Beta Launch Preparation**
- Beta user onboarding
- Documentation completion
- Organizer training materials
- Bug fixes from testing
- Monitoring setup
- Backup and recovery testing
- Legal compliance review

### Phase 6: Launch & Iteration (Weeks 23-24)
**Sprint 12 (Week 23-24): Soft Launch**
- Deploy to production
- Monitor system performance
- Gather user feedback
- Quick iteration on critical issues
- Marketing website launch
- App store submissions
- Initial user acquisition

## Key Technical Decisions

### 1. Why Extend Hi.Events Instead of Building from Scratch?
- **Pros**: Solid ticketing foundation, payment processing handled, admin panel exists
- **Cons**: Need to understand existing codebase, potential limitations
- **Decision**: Extension is faster to market with proven ticketing features

### 2. Why Monorepo?
- **Shared Code**: API client, types, utilities used by both web and mobile
- **Consistency**: Ensures synchronized deployments
- **Developer Experience**: Single repository for entire platform

### 3. Why PostgreSQL for Social Features?
- **Hi.Events Compatibility**: Already uses PostgreSQL
- **JSON Support**: Flexible for polls, reactions
- **Full-text Search**: Built-in search capabilities
- **Performance**: Handles complex queries well

### 4. Why React Native Over Flutter?
- **JavaScript Ecosystem**: Same language as web frontend
- **Hi.Events Compatibility**: Existing React components can inspire mobile
- **Expo**: Simplifies development and deployment
- **Community**: Larger ecosystem for event-related plugins

### 5. Why Pusher/Soketi for Real-time?
- **Laravel Integration**: First-class support in Laravel
- **Scalability**: Proven at scale
- **Self-hosting Option**: Soketi provides control
- **Client Libraries**: Available for web and React Native

## Security Considerations

### 1. Authentication & Authorization
- JWT tokens with refresh rotation
- Role-based access (Attendee, Organizer, Admin)
- Event-specific permissions
- Social login integration (Google, Facebook)

### 2. Content Moderation
- Automated image scanning for inappropriate content
- User reporting system
- Organizer moderation tools
- Rate limiting on posts/uploads

### 3. Data Privacy
- GDPR compliance for European expansion
- User data export functionality
- Right to deletion
- Consent management for photos

### 4. Payment Security
- PCI compliance through Stripe
- No credit card storage
- Secure webhook handling
- Fraud detection integration

## Performance Requirements

### 1. Response Times
- API responses: < 200ms (p95)
- Page loads: < 3s on 3G
- Real-time updates: < 100ms latency

### 2. Scalability Targets
- Support 10,000 concurrent users per event
- Handle 1,000 photo uploads per minute
- Process 10,000 ticket purchases per hour

### 3. Availability
- 99.9% uptime SLA
- Zero-downtime deployments
- Automatic failover
- Regular backups

## Monitoring & Analytics

### 1. Technical Monitoring
- Application Performance Monitoring (APM)
- Error tracking (Sentry)
- Uptime monitoring
- Database query performance

### 2. Business Metrics
- Daily/Monthly Active Users
- Ticket conversion rates
- Social engagement rates
- Photo capture/reveal rates
- Poll participation rates

### 3. User Analytics
- User journey tracking
- Feature adoption rates
- Retention metrics
- Churn analysis

## Third-party Services

### Required Services
1. **Pusher/Soketi**: Real-time messaging
2. **AWS S3/Cloudflare R2**: Photo storage
3. **Stripe Connect**: Payment processing (from Hi.Events)
4. **SendGrid/Postmark**: Transactional emails
5. **FCM/APNS**: Push notifications
6. **Cloudflare**: CDN and DDoS protection
7. **Sentry**: Error tracking
8. **Algolia** (optional): Advanced search

### Development Tools
1. **GitHub**: Version control
2. **GitHub Actions**: CI/CD
3. **Docker**: Containerization
4. **Vercel/Netlify**: Web app hosting
5. **Expo EAS**: Mobile app building
6. **Postman**: API documentation

## Future Considerations

### Phase 2 Features (Post-launch)
1. **AI-powered event recommendations**
2. **Video stories/reels during events**
3. **Virtual/hybrid event support**
4. **Influencer partnership tools**
5. **Advanced analytics with ML insights**
6. **Multi-language support**
7. **Cryptocurrency payments**

### Potential Integrations
1. **Instagram/TikTok**: Social sharing
2. **Spotify**: Event playlists
3. **Uber/Lyft**: Transportation booking
4. **Hotels.com**: Accommodation packages
5. **Google Calendar**: Event sync

## Success Metrics

### Technical KPIs
- Page load speed < 3s
- API uptime > 99.9%
- Mobile app crash rate < 0.5%
- Real-time message delivery > 99%

### Business KPIs
- Monthly Active Users growth
- Ticket sales conversion rate > 10%
- Social engagement rate > 30%
- Photo feature adoption > 50%
- Organizer retention > 80%

## Development Guidelines

### Code Standards
- Follow PSR-12 for PHP
- Use ESLint/Prettier for JavaScript/TypeScript
- Write tests for all new features (target 80% coverage)
- Document all API endpoints
- Use conventional commits

### Architecture Principles
1. **Mobile-first**: Design for mobile, adapt to desktop
2. **Offline-first**: Cache aggressively, sync when online
3. **Real-time by default**: Live updates where possible
4. **Progressive enhancement**: Core features work without JS
5. **Accessibility**: WCAG 2.1 AA compliance

### Testing Strategy
- Unit tests for business logic
- Integration tests for API endpoints
- E2E tests for critical user journeys
- Performance tests for load scenarios
- Security tests for vulnerabilities

## Deployment Strategy

### Environments
1. **Development**: Local Docker setup
2. **Staging**: Mirrors production
3. **Production**: Blue-green deployment

### Infrastructure
```yaml
Production:
  - Backend: AWS ECS or DigitalOcean App Platform
  - Database: AWS RDS or DigitalOcean Managed PostgreSQL
  - Redis: AWS ElastiCache or DigitalOcean Managed Redis
  - Storage: AWS S3 or Cloudflare R2
  - CDN: Cloudflare
  - Monitoring: Datadog or New Relic
```

### Release Process
1. Feature branch → Pull request
2. Automated tests run
3. Code review required
4. Merge to main
5. Auto-deploy to staging
6. Manual promote to production
7. Monitor metrics post-deployment

## Risk Mitigation

### Technical Risks
1. **Hi.Events limitations**: Maintain upgrade path, contribute back
2. **Real-time scaling**: Use Soketi clusters, implement fallbacks
3. **Photo storage costs**: Implement smart compression, lifecycle policies
4. **Mobile app rejection**: Follow store guidelines strictly

### Business Risks
1. **User adoption**: Focus on one city initially
2. **Organizer onboarding**: Provide white-glove service
3. **Competition**: Move fast, iterate based on feedback
4. **Seasonality**: Diversify event types

## Communication & Documentation

### Documentation Requirements
1. **API Documentation**: OpenAPI/Swagger spec
2. **Developer Guide**: Setup and contribution guidelines
3. **User Guide**: For organizers and attendees
4. **Mobile App Guide**: Store descriptions and screenshots
5. **Operations Runbook**: Deployment and troubleshooting

### Team Communication
- Daily standups during sprints
- Weekly progress reports
- Sprint retrospectives
- Architecture decision records (ADRs)

---

## Getting Started for AI Agent

### Initial Setup Commands
```bash
# Clone Hi.Events
git clone https://github.com/HiEventsDev/hi.events.git boleto-platform
cd boleto-platform

# Create monorepo structure
mkdir -p apps/{backend,web,mobile}
mkdir -p packages/{api-client,types,utils,ui}

# Move Hi.Events to backend
mv * apps/backend/

# Initialize monorepo
npm init -y
npm install -D turbo
```

### First Tasks
1. Study Hi.Events codebase structure
2. Create database migration for social features
3. Implement basic EventPost model and API
4. Set up Next.js project with TypeScript
5. Create shared types package
6. Implement authentication flow

### Questions to Address Early
1. How to handle Hi.Events updates?
2. Real-time service choice: Pusher vs Soketi?
3. Photo storage: S3 vs Cloudflare R2?
4. Mobile navigation: Stack vs Tab-based?
5. Staging environment specifications?

Remember: The goal is to ship an MVP within 24 weeks that demonstrates the social features while maintaining Hi.Events' reliable ticketing foundation. Focus on the core social experience first, then expand based on user feedback.