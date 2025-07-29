// Social Features Types for Bole.to Platform

export interface EventPost {
  id: number;
  event_id: number;
  user_id: number | null;
  attendee_id: number;
  content: string;
  type: 'text' | 'image' | 'poll' | 'announcement';
  media_url?: string;
  is_pinned: boolean;
  is_organizer_post: boolean;
  reply_to_id?: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // Relations
  reactions?: PostReaction[];
  replies?: EventPost[];
  author?: User;
  attendee?: Attendee;
}

export interface PostReaction {
  id: number;
  post_id: number;
  user_id: number;
  attendee_id: number;
  reaction_type: 'like' | 'fire' | 'laugh' | 'celebrate';
  created_at: string;
  
  // Relations
  user?: User;
  attendee?: Attendee;
}

export interface EventPhoto {
  id: number;
  event_id: number;
  attendee_id: number;
  photo_url: string;
  thumbnail_url?: string;
  blur_hash?: string;
  taken_at: string;
  reveal_at: string;
  is_revealed: boolean;
  view_count: number;
  created_at: string;
  
  // Relations
  attendee?: Attendee;
  interactions?: PhotoInteraction[];
}

export interface PhotoInteraction {
  id: number;
  photo_id: number;
  user_id: number;
  type: 'reaction' | 'comment';
  content?: string; // For comments
  reaction_type?: 'like' | 'fire' | 'laugh' | 'celebrate'; // For reactions
  created_at: string;
  
  // Relations
  user?: User;
}

export interface EventPoll {
  id: number;
  event_id: number;
  created_by: number;
  question: string;
  type: 'multiple_choice' | 'rating' | 'open_ended';
  options?: PollOption[];
  allows_multiple: boolean;
  is_anonymous: boolean;
  closes_at?: string;
  created_at: string;
  updated_at: string;
  
  // Relations
  responses?: PollResponse[];
  creator?: User;
}

export interface PollOption {
  id: string;
  text: string;
  color?: string;
}

export interface PollResponse {
  id: number;
  poll_id: number;
  user_id: number;
  attendee_id: number;
  selected_options: string[]; // Array of option IDs or values
  created_at: string;
  
  // Relations
  user?: User;
  attendee?: Attendee;
}

export interface UserPushToken {
  id: number;
  user_id: number;
  token: string;
  platform: 'ios' | 'android' | 'web';
  device_id?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface EventEngagementAnalytics {
  id: number;
  event_id: number;
  date: string;
  total_posts: number;
  total_reactions: number;
  total_photos: number;
  unique_engaged_users: number;
  poll_participation_rate?: number;
  created_at: string;
}

// Real-time event types
export interface RealTimeEvent {
  channel: string;
  event: string;
  data: any;
  timestamp: string;
}

export interface PostCreatedEvent extends RealTimeEvent {
  event: 'post.created';
  data: EventPost;
}

export interface ReactionAddedEvent extends RealTimeEvent {
  event: 'reaction.added';
  data: {
    post_id: number;
    reaction: PostReaction;
  };
}

export interface PhotoRevealedEvent extends RealTimeEvent {
  event: 'photo.revealed';
  data: {
    event_id: number;
    photos: EventPhoto[];
  };
}

// Extended types from Hi.Events
export interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  locale?: string;
  timezone?: string;
  created_at: string;
  updated_at: string;
}

export interface Attendee {
  id: number;
  event_id: number;
  order_id: number;
  ticket_id: number;
  first_name: string;
  last_name: string;
  email: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Event {
  id: number;
  title: string;
  description?: string;
  start_date: string;
  end_date?: string;
  location?: string;
  status: 'draft' | 'live' | 'archived';
  created_at: string;
  updated_at: string;
}

// API Response types
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
  links: {
    first: string;
    last: string;
    prev?: string;
    next?: string;
  };
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
  errors?: Record<string, string[]>;
}

// Form types
export interface CreatePostRequest {
  content: string;
  type: 'text' | 'image';
  media_url?: string;
  reply_to_id?: number;
}

export interface CreatePollRequest {
  question: string;
  type: 'multiple_choice' | 'rating' | 'open_ended';
  options?: PollOption[];
  allows_multiple?: boolean;
  is_anonymous?: boolean;
  closes_at?: string;
}

export interface CapturePhotoRequest {
  photo_data: string; // Base64 encoded image
  reveal_at?: string;
} 