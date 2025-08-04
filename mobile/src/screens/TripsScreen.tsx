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

export default function TripsScreen() {
  const [activeTab, setActiveTab] = useState<'upcoming' | 'past'>('upcoming');

  const upcomingTrips = [
    {
      id: '1',
      title: 'Summer Music Festival',
      date: 'Saturday, Aug 10, 2025',
      time: '6:00 PM - 11:00 PM',
      location: 'Central Park, NYC',
      type: 'General Admission',
      ticketNumber: 'SMF-2025-001',
      status: 'active',
    },
    {
      id: '2',
      title: 'Tech Conference 2025',
      date: 'Friday, Aug 16, 2025',
      time: '9:00 AM - 6:00 PM',
      location: 'Convention Center',
      type: 'VIP Experience',
      ticketNumber: 'TC-2025-042',
      status: 'warning',
    },
  ];

  const pastTrips = [
    {
      id: '3',
      title: 'Jazz Night Downtown',
      date: 'Friday, Jul 25, 2025',
      time: '8:00 PM - 12:00 AM',
      location: 'Blue Note Club',
      type: 'Premium Seat',
      ticketNumber: 'JN-2025-089',
      status: 'completed',
    },
  ];

  const trips = activeTab === 'upcoming' ? upcomingTrips : pastTrips;

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active':
        return colors.success;
      case 'warning':
        return colors.warning;
      case 'completed':
        return colors.gray[500];
      default:
        return colors.gray[500];
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'active':
        return '✓';
      case 'warning':
        return '!';
      case 'completed':
        return '✓';
      default:
        return '';
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={colors.jetBlack[950]} />
      
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>My Trips</Text>
        <TouchableOpacity>
          <Text style={styles.menuIcon}>⋯</Text>
        </TouchableOpacity>
      </View>

      {/* Tab Navigation */}
      <View style={styles.tabContainer}>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'upcoming' && styles.activeTab]}
          onPress={() => setActiveTab('upcoming')}
        >
          <Text style={[styles.tabText, activeTab === 'upcoming' && styles.activeTabText]}>
            Upcoming
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'past' && styles.activeTab]}
          onPress={() => setActiveTab('past')}
        >
          <Text style={[styles.tabText, activeTab === 'past' && styles.activeTabText]}>
            Past
          </Text>
        </TouchableOpacity>
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {trips.length > 0 ? (
          <View style={styles.tripsList}>
            {trips.map((trip) => (
              <View key={trip.id} style={styles.boardingPassCard}>
                {/* Perforation Line */}
                <View style={styles.perforationLine} />
                
                {/* Left Section - Event Info */}
                <View style={styles.leftSection}>
                  <Text style={styles.eventTitle}>{trip.title}</Text>
                  <Text style={styles.eventDate}>{trip.date}</Text>
                  <Text style={styles.eventTime}>{trip.time}</Text>
                  <Text style={styles.eventLocation}>📍 {trip.location}</Text>
                  <Text style={[styles.ticketType, trip.type === 'VIP Experience' && styles.vipType]}>
                    {trip.type}
                  </Text>
                  <Text style={styles.ticketNumber}>Ticket #{trip.ticketNumber}</Text>
                </View>

                {/* Right Section - QR Code */}
                <View style={styles.rightSection}>
                  <View style={styles.qrCode}>
                    <View style={styles.qrInner}>
                      <View style={styles.qrPattern} />
                    </View>
                  </View>
                  <Text style={styles.qrLabel}>QR Code</Text>
                  
                  {/* Status Indicator */}
                  <View style={[styles.statusIndicator, { backgroundColor: getStatusColor(trip.status) }]}>
                    <Text style={styles.statusIcon}>{getStatusIcon(trip.status)}</Text>
                  </View>
                </View>
              </View>
            ))}
          </View>
        ) : (
          <View style={styles.emptyState}>
            <Text style={styles.emptyTitle}>
              {activeTab === 'upcoming' ? 'No upcoming trips' : 'No past trips'}
            </Text>
            <Text style={styles.emptySubtitle}>
              {activeTab === 'upcoming' ? 'Book your next adventure!' : 'Your event history will appear here'}
            </Text>
            {activeTab === 'upcoming' && (
              <TouchableOpacity style={styles.exploreButton}>
                <Text style={styles.exploreButtonText}>Explore Events</Text>
              </TouchableOpacity>
            )}
          </View>
        )}

        {/* Swipe Hint */}
        {trips.length > 0 && activeTab === 'upcoming' && (
          <View style={styles.swipeHint}>
            <Text style={styles.swipeText}>Swipe left to archive</Text>
            <Text style={styles.swipeArrows}>← → →</Text>
          </View>
        )}
      </ScrollView>
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
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: colors.white,
  },
  menuIcon: {
    fontSize: 20,
    color: colors.white,
  },
  tabContainer: {
    flexDirection: 'row',
    backgroundColor: colors.gray[800],
    paddingHorizontal: 24,
    paddingBottom: 16,
  },
  tab: {
    paddingHorizontal: 20,
    paddingVertical: 8,
    borderRadius: 16,
    marginRight: 16,
  },
  activeTab: {
    backgroundColor: colors.horizonBlue[600],
  },
  tabText: {
    fontSize: 14,
    color: colors.gray[300],
  },
  activeTabText: {
    color: colors.white,
    fontWeight: '600',
  },
  content: {
    flex: 1,
  },
  tripsList: {
    paddingHorizontal: 20,
    paddingTop: 20,
    gap: 20,
  },
  boardingPassCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    overflow: 'hidden',
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    flexDirection: 'row',
    minHeight: 140,
  },
  perforationLine: {
    position: 'absolute',
    right: 80,
    top: 8,
    bottom: 8,
    width: 1,
    backgroundColor: colors.gray[300],
    borderStyle: 'dashed',
    borderWidth: 1,
    borderColor: colors.gray[300],
  },
  leftSection: {
    flex: 1,
    padding: 16,
    paddingRight: 90,
  },
  rightSection: {
    width: 80,
    padding: 12,
    alignItems: 'center',
    justifyContent: 'center',
    position: 'relative',
  },
  eventTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.jetBlack[950],
    marginBottom: 4,
  },
  eventDate: {
    fontSize: 12,
    color: colors.gray[600],
    marginBottom: 2,
  },
  eventTime: {
    fontSize: 12,
    color: colors.gray[600],
    marginBottom: 4,
  },
  eventLocation: {
    fontSize: 12,
    color: colors.gray[600],
    marginBottom: 8,
  },
  ticketType: {
    fontSize: 12,
    color: colors.horizonBlue[600],
    fontWeight: '600',
    marginBottom: 4,
  },
  vipType: {
    color: colors.sunsetOrange[600],
  },
  ticketNumber: {
    fontSize: 10,
    color: colors.gray[500],
  },
  qrCode: {
    width: 50,
    height: 50,
    backgroundColor: colors.jetBlack[950],
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  qrInner: {
    width: 40,
    height: 40,
    backgroundColor: colors.white,
    justifyContent: 'center',
    alignItems: 'center',
  },
  qrPattern: {
    width: 30,
    height: 30,
    backgroundColor: colors.jetBlack[950],
  },
  qrLabel: {
    fontSize: 10,
    color: colors.gray[600],
    textAlign: 'center',
  },
  statusIndicator: {
    position: 'absolute',
    top: 8,
    right: 8,
    width: 16,
    height: 16,
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
  },
  statusIcon: {
    fontSize: 10,
    color: colors.white,
    fontWeight: 'bold',
  },
  emptyState: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 40,
    paddingTop: 60,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 8,
    textAlign: 'center',
  },
  emptySubtitle: {
    fontSize: 14,
    color: colors.gray[300],
    textAlign: 'center',
    marginBottom: 24,
  },
  exploreButton: {
    backgroundColor: colors.horizonBlue[600],
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  exploreButtonText: {
    color: colors.white,
    fontSize: 14,
    fontWeight: '600',
  },
  swipeHint: {
    alignItems: 'center',
    paddingVertical: 20,
  },
  swipeText: {
    fontSize: 12,
    color: colors.gray[500],
    marginBottom: 4,
  },
  swipeArrows: {
    fontSize: 12,
    color: colors.gray[500],
  },
});