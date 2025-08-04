# Bole.to – Master Screen Catalogue v1.0
_Last updated: © 2025-08-03_

## 0. Legend
| 🏷 | Type                          | Notes                                  |
|----|------------------------------|----------------------------------------|
| **[M]** | Mobile-only               | RN / Expo screen                       |
| **[W]** | Web-only                  | Next.js page                           |
| **[∑]** | Shared (web + mobile)     | Same name / route on both clients      |
| **State** | Variants                | Empty ↔ Loading ↔ Error ↔ Populated    |

---

## 1. Access & On-Boarding

| ID | Screen | Type | Key Functions / States | Primary Entry |
|----|--------|------|------------------------|---------------|
| A-00 | Splash / App Loader | **[M]** | Branding animation → token check → nav decision | native boot |
| A-01 | Welcome / Get Started | ∑ | Language selector, SSO buttons | deep-link / logout |
| A-02 | Login | ∑ | Email + password, social login, remember me | A-01 |
| A-03 | Register | ∑ | Multi-step form (email → code → profile), T&C | A-01 |
| A-04 | Forgot Password | ∑ | Request reset, code verify, new pass | A-02 |
| A-05 | On-Boarding Wizard | ∑ | Pick interests, preferred cities, allow push | A-03 success |
| A-06 | Permissions Prompt | [M] | Camera, notifications, contacts | on demand |

---

## 2. Global Navigation

| ID | Screen | Tabs / Routes | Type |
|----|--------|---------------|------|
| G-00 | **Bottom-Tab / Navbar** | _Flights_ ✈︎ \| _Search_🔍 \| _Trips_🎫 \| _Social_💬 \| _Profile_👤 | ∑ |

---

## 3. Discovery & Search

| ID | Screen | Type | Key Functions |
|----|--------|------|---------------|
| D-01 | Home Feed ("Destinations") | ∑ | Hero carousel, personalised event cards, promo banner, infinite scroll, pull-to-refresh |
| D-02 | City Landing | ∑ | Upcoming events by category, map snippet, follow city |
| D-03 | Category Landing | ∑ | Music 🎵, Tech 💻, Art 🎨 … filter + sort |
| D-04 | Global Search | ∑ | Search-bar, recent queries, hot tags |
| D-05 | Results List | ∑ | Facets (date, price, distance), toggle list ↔ map |
| D-06 | Map Explore | [M] | Clustered pins, locate-me, bottom sheet details |

---

## 4. Event Experience

| ID | Screen | Type | Key Functions / States |
|----|--------|------|------------------------|
| E-01 | Event Details | ∑ | Banner, date/time, rich tabs (About · Tickets · Gallery · Chat · FAQ) |
| E-02 | Ticket Selector | ∑ | Tier list, quantity stepper, promo code |
| E-03 | Checkout | ∑ | Order summary, payment method, fees, miles earned |
| E-04 | Payment Success | ∑ | Confetti 🎉, add-to-wallet, share link |
| E-05 | Invite Friends | ∑ | Share sheet, contacts picker |
| E-06 | Event Chat / Wall | ∑ | Real-time messages, pinned posts, mod tools |
| E-07 | Gallery | ∑ | Media grid, full-screen swipe, upload (organiser) |
| E-08 | Venue Map | ∑ | Interactive layout, stage pins, amenities |

---

## 5. Trips & Tickets

| ID | Screen | Type | Key Functions |
|----|--------|------|---------------|
| T-01 | My Trips (Boarding Passes) | ∑ | Upcoming / Past tabs, swipe-to-archive |
| T-02 | Boarding Pass Detail | ∑ | Large QR, seat/zone, add to Apple/Google Wallet, transfer |
| T-03 | Trip Timeline | [M] | Pre-event tips, gate open count-down, check-in status |

---

## 6. Social Layer

| ID | Screen | Type | Key Functions |
|----|--------|------|---------------|
| S-01 | Social Feed | ∑ | Posts + likes, video autoplay, stories bar |
| S-02 | Create Post | ∑ | Photo/video picker, tagging, privacy toggle |
| S-03 | Post Detail | ∑ | Comments thread, share, report |
| S-04 | Messages List | ∑ | DMs + group chat preview |
| S-05 | Chat Room | ∑ | Reactions, attachments, typing indicator |
| S-06 | Story Viewer | [M] | Auto-advance, progress bar, swipe gestures |

---

## 7. Rewards (Miles)

| ID | Screen | Type | Key Functions |
|----|--------|------|---------------|
| R-01 | Miles Dashboard | ∑ | Balance, progress to next tier, recent accrual |
| R-02 | Tier Benefits | ∑ | Perks list, upgrade criteria |
| R-03 | Redeem Catalog | ∑ | Vouchers grid, filters, detail drawer |
| R-04 | Redemption Flow | ∑ | Confirm, OTP, success receipt |

---

## 8. Profile & Settings

| ID | Screen | Type | Key Functions |
|----|--------|------|---------------|
| P-01 | Public Profile | ∑ | Avatar, socials, follow/unfollow |
| P-02 | My Profile | ∑ | Edit photo, bio, interests |
| P-03 | Wallet / Payment Methods | ∑ | Cards list, add card, delete |
| P-04 | Notification Settings | ∑ | Toggles (push/email/SMS), quiet hours |
| P-05 | Security & Login | ∑ | Change password, 2FA toggle, device sessions |
| P-06 | Support Center | ∑ | FAQ list, open ticket, live chat |
| P-07 | Legal & Privacy | ∑ | ToS, Privacy Policy, licenses |
| P-08 | Debug / About (dev) | [W] | Build info, env vars |

---

## 9. Organizer Portal (min-MVP)

*(web-only admin but included for completeness)*

| ID | Screen | Type | Key Functions |
|----|--------|------|---------------|
| O-01 | Org Dashboard | **[W]** | KPIs, live sales, quick links |
| O-02 | Event Wizard (multi-step) | **[W]** | Details → Tickets → Media → Publish |
| O-03 | Orders List | **[W]** | Filters, CSV export, refund action |
| O-04 | Attendee Scanner | **[W]** | Web-camera QR, real-time status |
| O-05 | Payouts | **[W]** | Stripe Connect balance, history |

---

## 10. System & Utility

| ID | Screen | Type | Purpose |
|----|--------|------|---------|
| U-01 | 404 / Empty State | ∑ | Route fallback |
| U-02 | Maintenance Banner | ∑ | Feature flag controlled |
| U-03 | In-App Update Prompt | [M] | OTA updates via Expo |
| U-04 | Feature Flag Demo | [W] | QA internal |

---

### Appendix A – Cross-Link Matrix  
*(maintain in Figma or Excel for easy updates; omitted here for brevity)*

### Appendix B – Navigation Flowcharts  
*(`/docs/ux/flows/` auto-generated by ui-ux-master)*

---

> **Next action**  
> 1. Save this file as `docs/ux/screens.md`.  
> 2. Kick off the **`ui-ux-master`** agent with the prompt:  
> ```text
> Use docs/ux/screens.md as the definitive page list.  
> Produce low-fidelity wire-frames (mobile + web) for phase-1 critical screens:
> A-01 Welcome, D-01 Home, E-01 Event Details, T-01 My Trips.  
> Deliver in Figma JSON or SVG so fe-architect can derive the component map.
> ```  
> 3. Review the first wire frame batch and iterate on visual direction before
> moving to full hi-fi UI.

Feel free to add / remove screens or reorganise sections before handing the doc
to the agent – it will treat whatever is in `screens.md` as gospel.