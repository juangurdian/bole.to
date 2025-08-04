import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  SafeAreaView,
  StatusBar,
} from 'react-native';
import { colors } from '../constants/colors';
import type { RootStackParamList } from '../types/navigation';
import type { NativeStackScreenProps } from '@react-navigation/native-stack';

type Props = NativeStackScreenProps<RootStackParamList, 'EventDetails'>;

export default function EventDetailsScreen({ navigation }: Props) {
  const [activeTab, setActiveTab] = useState<'about' | 'tickets' | 'gallery' | 'chat' | 'faq'>('about');

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={colors.jetBlack[950]} />
      
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backButton}>← Back</Text>
        </TouchableOpacity>
        <TouchableOpacity>
          <Text style={styles.menuButton}>⋯</Text>
        </TouchableOpacity>
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {/* Event Banner */}
        <View style={styles.eventBanner}>
          <Text style={styles.eventTitle}>Summer Music Festival</Text>
          <Text style={styles.eventSubtitle}>Outdoor Concert Experience</Text>
          
          {/* Floating Action Buttons */}
          <View style={styles.floatingActions}>
            <TouchableOpacity style={styles.actionButton}>
              <Text style={styles.actionIcon}>♡</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionButton}>
              <Text style={styles.actionIcon}>↗</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Event Info Card */}
        <View style={styles.eventInfoCard}>
          <Text style={styles.eventDate}>Saturday, August 10, 2025</Text>
          <Text style={styles.eventTime}>6:00 PM - 11:00 PM</Text>
          <Text style={styles.eventLocation}>📍 Central Park, New York</Text>
          <Text style={styles.eventStats}>2.3k going • 5.1k interested</Text>
        </View>

        {/* Tab Navigation */}
        <View style={styles.tabContainer}>
          {['about', 'tickets', 'gallery', 'chat', 'faq'].map((tab) => (
            <TouchableOpacity
              key={tab}
              style={[styles.tab, activeTab === tab && styles.activeTab]}
              onPress={() => setActiveTab(tab as any)}
            >
              <Text style={[styles.tabText, activeTab === tab && styles.activeTabText]}>
                {tab.charAt(0).toUpperCase() + tab.slice(1)}
              </Text>
            </TouchableOpacity>
          ))}
        </View>

        {/* Tab Content */}
        <View style={styles.tabContent}>
          {activeTab === 'about' && (
            <View>
              <Text style={styles.sectionTitle}>About this event</Text>
              <Text style={styles.description}>
                Join us for an unforgettable night of music featuring top artists from around the world. 
                Food trucks, art installations, and more!
              </Text>
              
              <Text style={styles.sectionTitle}>Organizer</Text>
              <View style={styles.organizerInfo}>
                <View style={styles.organizerAvatar} />
                <View style={styles.organizerDetails}>
                  <Text style={styles.organizerName}>NYC Music Events</Text>
                  <Text style={styles.organizerVerified}>Verified organizer</Text>
                </View>
              </View>
            </View>
          )}

          {activeTab === 'tickets' && (
            <View>
              <Text style={styles.sectionTitle}>Select Tickets</Text>
              
              <View style={styles.ticketOption}>
                <View style={styles.ticketInfo}>
                  <Text style={styles.ticketTitle}>General Admission</Text>
                  <Text style={styles.ticketDescription}>Standing room, food included</Text>
                </View>
                <View style={styles.ticketPrice}>
                  <Text style={styles.priceText}>$45</Text>
                  <TouchableOpacity style={styles.addButton}>
                    <Text style={styles.addButtonText}>+ Add</Text>
                  </TouchableOpacity>
                </View>
              </View>

              <View style={styles.ticketOption}>
                <View style={styles.ticketInfo}>
                  <Text style={styles.ticketTitle}>VIP Experience</Text>
                  <Text style={styles.ticketDescription}>Reserved seating, premium bar</Text>
                </View>
                <View style={styles.ticketPrice}>
                  <Text style={styles.priceText}>$125</Text>
                  <TouchableOpacity style={styles.addButton}>
                    <Text style={styles.addButtonText}>+ Add</Text>
                  </TouchableOpacity>
                </View>
              </View>
            </View>
          )}

          {activeTab === 'gallery' && (
            <View style={styles.placeholderContent}>
              <Text style={styles.placeholderText}>Photo Gallery</Text>
              <Text style={styles.placeholderSubtext}>Event photos and videos will appear here</Text>
            </View>
          )}

          {activeTab === 'chat' && (
            <View style={styles.placeholderContent}>
              <Text style={styles.placeholderText}>Event Chat</Text>
              <Text style={styles.placeholderSubtext}>Connect with other attendees</Text>
            </View>
          )}

          {activeTab === 'faq' && (
            <View style={styles.placeholderContent}>
              <Text style={styles.placeholderText}>FAQ</Text>
              <Text style={styles.placeholderSubtext}>Frequently asked questions about this event</Text>
            </View>
          )}
        </View>
      </ScrollView>

      {/* Sticky CTA Button */}
      <View style={styles.stickyFooter}>
        <TouchableOpacity style={styles.ctaButton}>
          <Text style={styles.ctaButtonText}>Get Tickets - From $45</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.jetBlack[950],
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 24,
    paddingVertical: 16,
    backgroundColor: colors.gray[800],
  },
  backButton: {
    fontSize: 16,
    color: colors.white,
  },
  menuButton: {
    fontSize: 20,
    color: colors.white,
  },
  content: {
    flex: 1,
  },
  eventBanner: {
    height: 200,
    backgroundColor: colors.horizonBlue[600],
    justifyContent: 'center',
    alignItems: 'center',
    position: 'relative',
  },
  eventTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 8,
    textAlign: 'center',
  },
  eventSubtitle: {
    fontSize: 16,
    color: colors.white,
    textAlign: 'center',
  },
  floatingActions: {
    position: 'absolute',
    right: 16,
    bottom: 16,
    flexDirection: 'row',
    gap: 8,
  },
  actionButton: {
    width: 40,
    height: 40,
    backgroundColor: colors.white,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
  },
  actionIcon: {
    fontSize: 18,
    color: colors.jetBlack[950],
  },
  eventInfoCard: {
    backgroundColor: colors.gray[800],
    margin: 20,
    padding: 20,
    borderRadius: 12,
  },
  eventDate: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 4,
  },
  eventTime: {
    fontSize: 14,
    color: colors.gray[300],
    marginBottom: 4,
  },
  eventLocation: {
    fontSize: 14,
    color: colors.gray[300],
    marginBottom: 8,
  },
  eventStats: {
    fontSize: 14,
    color: colors.horizonBlue[500],
    fontWeight: '600',
  },
  tabContainer: {
    flexDirection: 'row',
    backgroundColor: colors.gray[800],
    paddingHorizontal: 20,
    paddingVertical: 8,
  },
  tab: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 6,
    marginRight: 8,
  },
  activeTab: {
    backgroundColor: colors.horizonBlue[600],
  },
  tabText: {
    fontSize: 12,
    color: colors.gray[300],
    textTransform: 'capitalize',
  },
  activeTabText: {
    color: colors.white,
    fontWeight: '600',
  },
  tabContent: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 12,
  },
  description: {
    fontSize: 14,
    color: colors.gray[300],
    lineHeight: 20,
    marginBottom: 24,
  },
  organizerInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  organizerAvatar: {
    width: 40,
    height: 40,
    backgroundColor: colors.gray[600],
    borderRadius: 20,
    marginRight: 12,
  },
  organizerDetails: {
    flex: 1,
  },
  organizerName: {
    fontSize: 16,
    fontWeight: '600',
    color: colors.white,
    marginBottom: 2,
  },
  organizerVerified: {
    fontSize: 12,
    color: colors.success,
  },
  ticketOption: {
    flexDirection: 'row',
    backgroundColor: colors.gray[800],
    padding: 16,
    borderRadius: 8,
    marginBottom: 12,
    alignItems: 'center',
  },
  ticketInfo: {
    flex: 1,
  },
  ticketTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 4,
  },
  ticketDescription: {
    fontSize: 12,
    color: colors.gray[300],
  },
  ticketPrice: {
    alignItems: 'flex-end',
  },
  priceText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 8,
  },
  addButton: {
    backgroundColor: colors.horizonBlue[600],
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 4,
  },
  addButtonText: {
    fontSize: 12,
    color: colors.white,
    fontWeight: '600',
  },
  placeholderContent: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  placeholderText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 8,
  },
  placeholderSubtext: {
    fontSize: 14,
    color: colors.gray[300],
    textAlign: 'center',
  },
  stickyFooter: {
    padding: 20,
    backgroundColor: colors.jetBlack[950],
    borderTopWidth: 1,
    borderTopColor: colors.gray[800],
  },
  ctaButton: {
    backgroundColor: colors.horizonBlue[600],
    paddingVertical: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  ctaButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.white,
  },
});