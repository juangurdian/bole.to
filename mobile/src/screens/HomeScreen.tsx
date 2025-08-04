import React from 'react';
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
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { RootStackParamList } from '../types/navigation';

type NavigationProp = NativeStackNavigationProp<RootStackParamList>;

export default function HomeScreen() {
  const navigation = useNavigation<NavigationProp>();
  const events = [
    {
      id: '1',
      title: 'Summer Music Festival',
      date: 'Sat, Aug 10',
      price: '$75',
      location: 'Central Park',
      interested: '2.3k',
    },
    {
      id: '2',
      title: 'Tech Conference 2025',
      date: 'Fri, Aug 16',
      price: '$125',
      location: 'Convention Center',
      interested: '1.8k',
    },
    {
      id: '3',
      title: 'Art Gallery Opening',
      date: 'Sun, Aug 25',
      price: 'Free',
      location: 'Downtown Gallery',
      interested: '856',
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={colors.jetBlack[950]} />
      
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Destinations</Text>
        <View style={styles.headerActions}>
          <TouchableOpacity style={styles.iconButton}>
            <Text style={styles.iconText}>🔍</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.iconButton}>
            <Text style={styles.iconText}>👤</Text>
          </TouchableOpacity>
        </View>
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {/* Hero Carousel */}
        <View style={styles.heroCarousel}>
          <View style={styles.heroCard}>
            <Text style={styles.heroTitle}>Featured Event</Text>
            <Text style={styles.heroSubtitle}>Summer Music Festival</Text>
            <View style={styles.carouselDots}>
              <View style={[styles.dot, styles.activeDot]} />
              <View style={styles.dot} />
              <View style={styles.dot} />
            </View>
          </View>
        </View>

        {/* Promo Banner */}
        <View style={styles.promoBanner}>
          <Text style={styles.promoText}>🎉 Early Bird: 20% off selected events</Text>
        </View>

        {/* Section Header */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>For You</Text>
          <TouchableOpacity>
            <Text style={styles.seeAll}>See all</Text>
          </TouchableOpacity>
        </View>

        {/* Event Cards */}
        <View style={styles.eventsList}>
          {events.map((event) => (
            <TouchableOpacity 
              key={event.id} 
              style={styles.eventCard}
              onPress={() => navigation.navigate('EventDetails', { eventId: event.id })}
            >
              <View style={styles.eventImage} />
              <View style={styles.eventInfo}>
                <Text style={styles.eventTitle}>{event.title}</Text>
                <Text style={styles.eventMeta}>
                  {event.date} • {event.price}
                </Text>
                <Text style={styles.eventLocation}>{event.location}</Text>
                <Text style={styles.eventInterested}>
                  {event.interested} interested
                </Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        {/* Pull to Refresh Indicator */}
        <View style={styles.refreshIndicator}>
          <Text style={styles.refreshText}>Pull to refresh</Text>
        </View>
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
  headerActions: {
    flexDirection: 'row',
    gap: 8,
  },
  iconButton: {
    width: 32,
    height: 32,
    backgroundColor: colors.gray[600],
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
  },
  iconText: {
    fontSize: 16,
  },
  content: {
    flex: 1,
  },
  heroCarousel: {
    margin: 20,
    marginBottom: 16,
  },
  heroCard: {
    height: 140,
    backgroundColor: colors.horizonBlue[600],
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    position: 'relative',
  },
  heroTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 8,
  },
  heroSubtitle: {
    fontSize: 14,
    color: colors.white,
    marginBottom: 16,
  },
  carouselDots: {
    flexDirection: 'row',
    gap: 8,
    position: 'absolute',
    bottom: 16,
  },
  dot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: 'rgba(255, 255, 255, 0.5)',
  },
  activeDot: {
    backgroundColor: colors.white,
  },
  promoBanner: {
    marginHorizontal: 20,
    marginBottom: 24,
    paddingVertical: 12,
    backgroundColor: colors.sunsetOrange[600],
    borderRadius: 8,
    alignItems: 'center',
  },
  promoText: {
    fontSize: 14,
    color: colors.white,
    fontWeight: '500',
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.white,
  },
  seeAll: {
    fontSize: 14,
    color: colors.horizonBlue[500],
  },
  eventsList: {
    paddingHorizontal: 20,
    gap: 16,
  },
  eventCard: {
    flexDirection: 'row',
    backgroundColor: colors.gray[800],
    borderRadius: 8,
    padding: 16,
    alignItems: 'center',
  },
  eventImage: {
    width: 60,
    height: 60,
    backgroundColor: colors.gray[600],
    borderRadius: 8,
    marginRight: 16,
  },
  eventInfo: {
    flex: 1,
  },
  eventTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.white,
    marginBottom: 4,
  },
  eventMeta: {
    fontSize: 12,
    color: colors.gray[300],
    marginBottom: 2,
  },
  eventLocation: {
    fontSize: 12,
    color: colors.gray[300],
    marginBottom: 4,
  },
  eventInterested: {
    fontSize: 12,
    color: colors.horizonBlue[500],
  },
  refreshIndicator: {
    alignItems: 'center',
    paddingVertical: 20,
  },
  refreshText: {
    fontSize: 12,
    color: colors.gray[500],
  },
});