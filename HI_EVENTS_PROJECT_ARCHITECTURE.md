# Hi.Events Project Architecture Documentation

## Table of Contents
1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [Technology Stack](#technology-stack)
4. [Project Structure](#project-structure)
5. [Backend Architecture](#backend-architecture)
6. [Frontend Architecture](#frontend-architecture)
7. [Database Design](#database-design)
8. [Security Architecture](#security-architecture)
9. [Payment System Architecture](#payment-system-architecture)
10. [Deployment Architecture](#deployment-architecture)
11. [Development Workflow](#development-workflow)
12. [Scalability Considerations](#scalability-considerations)

## Overview

Hi.Events is a comprehensive **self-hosted event management and ticketing platform** designed as a modern, scalable SaaS solution. The platform enables organizations to create, manage, and sell tickets for events of all sizes, from small workshops to large conferences and concerts.

### Key Characteristics
- **Multi-tenant Architecture**: Account-based isolation with role-based access control
- **Microservices-Ready**: Clean separation between backend API and frontend client
- **Payment-First Design**: Built around Stripe Connect with marketplace capabilities
- **Mobile-Responsive**: Progressive web app features with cross-device compatibility
- **Internationalization**: Multi-language support for global deployment
- **Self-Hosted**: Complete control over data and deployment environment

## System Architecture

### High-Level Architecture Diagram
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend       │    │   Database      │
│   (React SPA)   │◄──►│   (Laravel API)  │◄──►│  (PostgreSQL)   │
│                 │    │                  │    │                 │
│ • Admin Panel   │    │ • REST API       │    │ • Multi-tenant  │
│ • Public Pages  │    │ • JWT Auth       │    │ • ACID Compliant│
│ • Widget        │    │ • Background Jobs│    │ • Full-text     │
└─────────────────┘    └──────────────────┘    └─────────────────┘
         │                       │                       │
         │                       ▼                       │
         │              ┌─────────────────┐              │
         │              │     Redis       │              │
         │              │  (Cache/Queue)  │              │
         │              └─────────────────┘              │
         │                       │                       │
         ▼                       ▼                       │
┌─────────────────┐    ┌──────────────────┐              │
│  File Storage   │    │ External Services│              │
│  (Local/S3)     │    │ • Stripe Connect │              │
│                 │    │ • Email Provider │              │
│ • Event Images  │    │ • Webhooks       │              │
│ • Exports       │    │ • PDF Generation │              │
└─────────────────┘    └──────────────────┘              │
                                                          │
                              ┌─────────────────────────────┘
                              ▼
                    ┌─────────────────┐
                    │   Monitoring    │
                    │                 │
                    │ • Application   │
                    │ • Database      │
                    │ • Infrastructure│
                    └─────────────────┘
```

### Architectural Patterns
1. **Clean Architecture**: Clear separation of concerns with domain-driven design
2. **CQRS Pattern**: Command-Query Responsibility Segregation for complex operations
3. **Repository Pattern**: Abstracted data access layer with interface contracts
4. **Service Layer Pattern**: Business logic encapsulation in service classes
5. **Event-Driven Architecture**: Loose coupling through domain events

## Technology Stack

### Backend Technologies
| Component | Technology | Version | Purpose |
|-----------|------------|---------|---------|
| **Framework** | Laravel | 11.x | PHP web framework with robust ecosystem |
| **Language** | PHP | 8.3+ | Modern PHP with strong typing and performance |
| **Database** | PostgreSQL | 15+ | ACID compliance, advanced indexing, JSON support |
| **Cache/Queue** | Redis | 7.x | High-performance caching and queue management |
| **Authentication** | JWT | 2.x | Stateless authentication with token management |
| **File Storage** | Local/S3 | - | Flexible file storage with cloud support |
| **PDF Generation** | DomPDF | 2.x | Server-side PDF generation for invoices |
| **Email** | Configurable | - | SMTP, SES, Mailgun, or other providers |

### Frontend Technologies
| Component | Technology | Version | Purpose |
|-----------|------------|---------|---------|
| **Framework** | React | 18.x | Modern UI framework with hooks and concurrent features |
| **Language** | TypeScript | 5.x | Type safety and enhanced developer experience |
| **Build Tool** | Vite | 5.x | Fast development and optimized production builds |
| **UI Library** | Mantine | 7.x | Comprehensive component library with theming |
| **State Management** | TanStack Query | 5.x | Server state management with automatic caching |
| **Global State** | Zustand | 4.x | Lightweight global state for client-side data |
| **Routing** | React Router | 6.x | Client-side routing with SSR support |
| **Styling** | SCSS | - | Enhanced CSS with variables and mixins |
| **I18n** | Lingui | 4.x | Internationalization with extraction tools |

### Infrastructure Technologies
| Component | Technology | Purpose |
|-----------|------------|---------|
| **Web Server** | Nginx | Reverse proxy, static file serving, load balancing |
| **Process Manager** | Supervisor | Background job processing and service management |
| **Containerization** | Docker | Application packaging and deployment |
| **Orchestration** | Docker Compose | Multi-container application management |
| **SSL/TLS** | Let's Encrypt | Automated SSL certificate management |

## Project Structure

### Root Directory Layout
```
hi-events/
├── backend/                 # Laravel API application
├── frontend/               # React client application  
├── docker/                 # Docker configurations
│   ├── all-in-one/        # Single container deployment
│   ├── development/       # Development environment
│   ├── backend/           # Backend-only container
│   └── frontend/          # Frontend-only container
├── misc/                   # Utilities and tools
├── docs/                   # Documentation (README in multiple languages)
└── deployment configs      # Various platform deployment templates
```

### Backend Structure (`/backend/`)
```
backend/
├── app/
│   ├── DomainObjects/         # Rich domain models (business logic)
│   ├── Services/              # Three-tier service architecture
│   │   ├── Application/       # Application layer (handlers)
│   │   ├── Domain/           # Domain services
│   │   └── Infrastructure/   # Infrastructure services
│   ├── Http/
│   │   ├── Actions/          # Action-based controllers
│   │   ├── Middleware/       # HTTP middleware
│   │   └── Requests/         # Form request validation
│   ├── Models/               # Eloquent models (data layer)
│   ├── Repository/           # Repository pattern implementation
│   │   ├── Interfaces/       # Repository contracts
│   │   └── Eloquent/        # Eloquent implementations
│   ├── Jobs/                 # Background job processing
│   ├── Mail/                 # Email templates and logic
│   ├── Events/              # Domain events
│   ├── Listeners/           # Event listeners
│   ├── Exceptions/          # Custom exception classes
│   └── Validators/          # Business rule validation
├── database/
│   ├── migrations/          # Database schema evolution
│   ├── seeders/            # Test data and initial setup
│   └── factories/          # Model factories for testing
├── config/                  # Application configuration
├── routes/                  # API route definitions
├── tests/                   # Automated test suites
└── storage/                # File storage and logs
```

### Frontend Structure (`/frontend/`)
```
frontend/
├── src/
│   ├── components/           # Reusable UI components
│   │   ├── common/          # Shared components
│   │   ├── forms/           # Form components
│   │   ├── layouts/         # Layout components
│   │   ├── modals/          # Modal dialogs
│   │   └── routes/          # Page components
│   ├── api/                 # API client modules
│   ├── queries/             # TanStack Query hooks (GET operations)
│   ├── mutations/           # TanStack Query hooks (POST/PUT/DELETE)
│   ├── stores/              # Global state management
│   ├── hooks/               # Custom React hooks
│   ├── utilities/           # Helper functions
│   ├── locales/             # Internationalization files
│   ├── constants/           # Application constants
│   └── styles/              # Global styles and themes
├── public/                  # Static assets
│   ├── images/             # Image assets
│   ├── sounds/             # Audio files (check-in sounds)
│   └── widget.js           # Embeddable widget script
└── build configs            # Vite, TypeScript, and tool configurations
```

## Backend Architecture

### Domain-Driven Design Implementation

#### Domain Objects Layer
Rich domain models that encapsulate business logic:
- **EventDomainObject**: Event lifecycle, validation, URL generation
- **ProductDomainObject**: Pricing logic, availability calculations
- **OrderDomainObject**: Financial calculations, status workflows
- **AttendeeDomainObject**: Check-in logic, ticket generation

#### Service Layer Architecture
Three-tier service architecture following hexagonal architecture principles:

1. **Application Layer** (`/Services/Application/Handlers/`)
   - Use case handlers (CreateEventHandler, ProcessOrderHandler)
   - Coordinate domain services and infrastructure
   - Handle cross-cutting concerns (validation, authorization)

2. **Domain Layer** (`/Services/Domain/`)
   - Pure business logic services
   - Domain event publishing
   - Business rule enforcement

3. **Infrastructure Layer** (`/Services/Infrastructure/`)
   - External service integration (Stripe, email)
   - File storage operations
   - Third-party API clients

#### Repository Pattern
Interface-driven data access with multiple implementations:
- **Interfaces**: Contract definitions for data operations
- **Eloquent**: Laravel ORM implementations
- **Caching**: Redis-based caching layer
- **Testing**: In-memory implementations for testing

### API Design Principles

#### RESTful API Structure
- **Resource-based URLs**: `/events/{event_id}/products`
- **HTTP verb semantics**: GET, POST, PUT, DELETE with proper usage
- **Status codes**: Consistent HTTP status code usage
- **Error responses**: Standardized error format with details

#### Authentication & Authorization
- **JWT tokens**: Stateless authentication with account context
- **Role-based access**: Admin and Organizer roles with entity-level permissions
- **Multi-tenant isolation**: Account-scoped data access

#### Rate Limiting & Security
- **Throttling**: Request rate limiting per endpoint
- **Input validation**: Comprehensive request validation
- **CORS**: Proper cross-origin resource sharing configuration
- **CSRF protection**: Token-based CSRF protection for web routes

## Frontend Architecture

### Component Architecture

#### Layout System
- **AppLayout**: Main admin interface with navigation
- **AuthLayout**: Authentication flow wrapper
- **PublicLayout**: Customer-facing pages
- **WidgetLayout**: Embeddable widget interface

#### Component Organization
- **Feature-based**: Components organized by business feature
- **Reusability**: Shared components with prop-based customization
- **Composition**: Higher-order components and render props patterns
- **Type safety**: Full TypeScript coverage with strict mode

### State Management

#### Server State (TanStack Query)
- **Automatic caching**: Intelligent cache management with stale-while-revalidate
- **Background refetching**: Keep data fresh without user interaction
- **Optimistic updates**: Immediate UI updates with rollback capability
- **Error boundaries**: Graceful error handling and recovery

#### Client State (Zustand)
- **Minimal global state**: Only truly global data (user session, theme)
- **Persistence**: Local storage integration for user preferences
- **DevTools**: Development debugging support

### Routing Architecture

#### Route Organization
- **Public routes**: `/e/` for events, `/checkout/` for purchases
- **Admin routes**: `/manage/` for event management
- **Auth routes**: `/auth/` for authentication flows
- **Widget routes**: `/widget/` for embeddable components

#### SSR Support
- **React Router 7**: Built-in SSR capabilities
- **SEO optimization**: Meta tags and structured data
- **Performance**: Code splitting and lazy loading

## Database Design

### Multi-Tenant Architecture

#### Account Isolation
- **Account scoping**: All data belongs to an account
- **User-account mapping**: Many-to-many relationship with roles
- **Data isolation**: Repository-level account filtering

#### Entity Relationships
```
Account (1) ──── (M) Events ──── (M) Products ──── (M) Orders ──── (M) Attendees
   │                    │             │               │              │
   │                    │             │               │              └─ Payments
   │                    │             │               └─ Invoices      
   │                    │             └─ Categories                     
   │                    └─ Settings                                     
   └─ Users (M-to-M with roles)                                       
```

### Data Models

#### Core Entities
1. **Account**: Multi-tenant root with payment configuration
2. **Event**: Central entity with rich metadata and settings
3. **Product**: Flexible product system supporting tickets and merchandise
4. **Order**: Complex financial entity with multiple payment providers
5. **Attendee**: Individual participants with check-in capabilities

#### Financial Entities
1. **Invoice**: Professional invoicing with PDF generation
2. **Payment**: Stripe payment tracking with webhook integration
3. **Refund**: Comprehensive refund management
4. **Application Fee**: Marketplace fee tracking

#### Operational Entities
1. **Check-in List**: Organized entry management
2. **Webhook**: External integration support
3. **Message**: Event communication system
4. **Question**: Custom form system with flexible answers

### Database Performance

#### Indexing Strategy
- **Primary keys**: BIGINT identity columns for scalability
- **Foreign keys**: Comprehensive foreign key indexing
- **Composite indexes**: Multi-column indexes for common queries
- **Partial indexes**: Conditional indexing for soft-deleted records
- **GIN indexes**: JSON and full-text search optimization

#### Search Capabilities
- **pg_trgm extension**: Trigram-based fuzzy text search
- **Full-text search**: PostgreSQL native search with ranking
- **JSON queries**: Efficient JSONB querying with GIN indexes

## Security Architecture

### Authentication Security

#### JWT Implementation
- **HS256 algorithm**: HMAC-based signatures for token integrity
- **Account context**: Account ID embedded in token payload
- **Token expiration**: 7-day tokens with 2-week refresh capability
- **Blacklisting**: Revoked token tracking for security

#### Password Security
- **bcrypt hashing**: Industry-standard password hashing
- **Reset tokens**: Secure random token generation with expiration
- **Rate limiting**: Throttling for brute force protection

### Authorization Security

#### Role-Based Access Control
- **Hierarchical roles**: Admin > Organizer role hierarchy
- **Entity-level permissions**: Granular access control per resource
- **Account isolation**: Strict multi-tenant data separation

#### Data Access Patterns
- **Repository scoping**: Account-level data filtering
- **Query guard clauses**: Automatic account context validation
- **Authorization middleware**: Centralized permission checking

### API Security

#### Input Validation
- **Laravel validation**: Comprehensive request validation rules
- **Data Transfer Objects**: Type-safe data structures
- **SQL injection prevention**: Parameterized queries and ORM protection

#### Cross-Origin Security
- **CORS configuration**: Proper origin validation
- **Content Security Policy**: XSS protection headers
- **CSRF protection**: Token-based CSRF validation

### Payment Security

#### Stripe Integration Security
- **Webhook signatures**: Cryptographic verification of webhook payloads
- **Connected accounts**: Isolated customer and payment data
- **PCI compliance**: No sensitive payment data storage

#### Data Protection
- **Encryption in transit**: TLS 1.3 for all communications
- **Sensitive data handling**: Minimal PII storage with encryption
- **Audit logging**: Comprehensive security event logging

## Payment System Architecture

### Stripe Connect Integration

#### Account Management
- **Express accounts**: Simplified onboarding for organizers
- **Standard accounts**: Full Stripe Dashboard access
- **Custom accounts**: White-label integration capability

#### Payment Flow
```
Customer → Frontend → Backend → Stripe API → Webhook → Order Processing
    │          │         │          │           │           │
    └─ Card ───┴─ Intent ┴─ Create ──┴─ Process ─┴─ Confirm ─┘
```

#### Marketplace Features
- **Application fees**: Platform revenue sharing
- **Connected payouts**: Direct organizer payments
- **Multi-party refunds**: Automatic fee handling

### Financial Architecture

#### Order Processing Pipeline
1. **Reservation**: Temporary inventory hold
2. **Payment intent**: Stripe payment setup
3. **Confirmation**: Payment processing
4. **Fulfillment**: Ticket delivery and activation
5. **Settlement**: Financial reconciliation

#### Tax and Fee Calculation
- **Calculation engine**: Flexible tax and fee rules
- **Multi-jurisdiction**: Support for various tax requirements
- **Rollup reporting**: Aggregated financial reporting

## Deployment Architecture

### Container Strategy

#### All-in-One Container
- **Single container**: PHP-FPM, Nginx, Node.js, and Supervisor
- **External services**: PostgreSQL and Redis as separate containers
- **Use case**: Simple deployments and getting started

#### Microservices Container
- **Backend container**: PHP-FPM with queue workers
- **Frontend container**: Node.js with SSR support
- **Web container**: Nginx reverse proxy
- **Use case**: Scalable production deployments

### Platform Support

#### Cloud Platforms
- **DigitalOcean**: Droplet and App Platform support
- **AWS**: EC2, ECS, and Vapor (serverless) support
- **Google Cloud**: Compute Engine and Cloud Run
- **Render**: Automated deployments with database

#### Self-Hosted Options
- **Docker Compose**: Complete multi-container setup
- **Traditional hosting**: LAMP/LEMP stack compatibility
- **VPS deployment**: Ubuntu/CentOS installation scripts

### Scalability Considerations

#### Horizontal Scaling
- **Stateless backend**: JWT authentication enables load balancing
- **Database connection pooling**: PgBouncer for connection management
- **CDN integration**: Static asset distribution
- **Queue workers**: Horizontal scaling of background jobs

#### Performance Optimization
- **Redis caching**: Multi-layer caching strategy
- **Database optimization**: Query optimization and indexing
- **Asset optimization**: Vite-based bundling and compression
- **Image optimization**: Responsive image delivery

#### Monitoring and Observability
- **Application monitoring**: Error tracking and performance metrics
- **Database monitoring**: Query performance and connection tracking
- **Infrastructure monitoring**: Server metrics and alerting
- **User analytics**: Event tracking and conversion metrics

## Development Workflow

### Local Development Setup
1. **Docker development environment**: Consistent development environment
2. **Hot reloading**: Fast development feedback loop
3. **Database seeding**: Sample data for development
4. **Test environment**: Automated testing setup

### Code Quality Standards
- **PHP**: PSR-12 coding standards with PHP-CS-Fixer
- **TypeScript**: Strict TypeScript configuration
- **Testing**: PHPUnit for backend, Jest/RTL for frontend
- **Linting**: ESLint and Prettier for JavaScript/TypeScript

### CI/CD Pipeline
- **Automated testing**: Unit, integration, and end-to-end tests
- **Code quality checks**: Static analysis and code coverage
- **Security scanning**: Dependency vulnerability scanning
- **Deployment automation**: Automated deployment to staging and production

This architecture document provides a comprehensive overview of the Hi.Events platform, detailing how each component works together to create a robust, scalable event management solution. The architecture is designed for growth, security, and maintainability while providing excellent user experience across all interfaces.