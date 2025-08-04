import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  StatusBar,
  SafeAreaView,
} from 'react-native';
import { colors } from '../constants/colors';
import type { RootStackParamList } from '../types/navigation';
import type { NativeStackScreenProps } from '@react-navigation/native-stack';

type Props = NativeStackScreenProps<RootStackParamList, 'Welcome'>;

export default function WelcomeScreen({ navigation }: Props) {
  const handleContinue = () => {
    navigation.navigate('Main');
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={colors.jetBlack[950]} />
      
      <View style={styles.content}>
        {/* Logo Area */}
        <View style={styles.logoContainer}>
          <View style={styles.logoPlaceholder}>
            <Text style={styles.logoText}>Bole.to</Text>
          </View>
          <Text style={styles.tagline}>Buy a ticket,</Text>
          <Text style={styles.tagline}>board the party ✈️</Text>
        </View>

        {/* Language Selector */}
        <TouchableOpacity style={styles.languageSelector}>
          <Text style={styles.languageText}>🌐 Language: English</Text>
          <Text style={styles.chevron}>▼</Text>
        </TouchableOpacity>

        {/* Social Login Buttons */}
        <View style={styles.socialButtons}>
          <TouchableOpacity 
            style={[styles.button, styles.facebookButton]}
            onPress={handleContinue}
          >
            <Text style={styles.buttonText}>Continue with Facebook</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[styles.button, styles.googleButton]}
            onPress={handleContinue}
          >
            <Text style={styles.buttonText}>Continue with Google</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[styles.button, styles.appleButton]}
            onPress={handleContinue}
          >
            <Text style={styles.buttonText}>Continue with Apple</Text>
          </TouchableOpacity>
        </View>

        {/* Divider */}
        <View style={styles.divider}>
          <View style={styles.dividerLine} />
          <Text style={styles.dividerText}>or</Text>
          <View style={styles.dividerLine} />
        </View>

        {/* Email/Register Buttons */}
        <View style={styles.actionButtons}>
          <TouchableOpacity 
            style={styles.loginButton}
            onPress={handleContinue}
          >
            <Text style={styles.loginButtonText}>Login</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.registerButton}
            onPress={handleContinue}
          >
            <Text style={styles.registerButtonText}>Register</Text>
          </TouchableOpacity>
        </View>

        {/* Terms */}
        <Text style={styles.termsText}>
          By continuing, you agree to our Terms
        </Text>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.jetBlack[950],
  },
  content: {
    flex: 1,
    paddingHorizontal: 24,
    justifyContent: 'center',
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: 60,
  },
  logoPlaceholder: {
    width: 120,
    height: 60,
    backgroundColor: colors.gray[300],
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 30,
  },
  logoText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.jetBlack[950],
  },
  tagline: {
    fontSize: 18,
    color: colors.white,
    textAlign: 'center',
    lineHeight: 24,
  },
  languageSelector: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.gray[600],
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    marginBottom: 40,
  },
  languageText: {
    fontSize: 14,
    color: colors.white,
  },
  chevron: {
    fontSize: 14,
    color: colors.white,
  },
  socialButtons: {
    gap: 12,
    marginBottom: 32,
  },
  button: {
    paddingVertical: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  facebookButton: {
    backgroundColor: '#4267B2',
  },
  googleButton: {
    backgroundColor: '#DB4437',
  },
  appleButton: {
    backgroundColor: colors.jetBlack[900],
  },
  buttonText: {
    color: colors.white,
    fontSize: 16,
    fontWeight: '500',
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 32,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: colors.gray[600],
  },
  dividerText: {
    marginHorizontal: 16,
    fontSize: 14,
    color: colors.gray[300],
  },
  actionButtons: {
    flexDirection: 'row',
    gap: 12,
    marginBottom: 24,
  },
  loginButton: {
    flex: 1,
    paddingVertical: 16,
    borderWidth: 1,
    borderColor: colors.horizonBlue[600],
    borderRadius: 8,
    alignItems: 'center',
  },
  loginButtonText: {
    color: colors.horizonBlue[600],
    fontSize: 16,
    fontWeight: '500',
  },
  registerButton: {
    flex: 1,
    paddingVertical: 16,
    backgroundColor: colors.horizonBlue[600],
    borderRadius: 8,
    alignItems: 'center',
  },
  registerButtonText: {
    color: colors.white,
    fontSize: 16,
    fontWeight: '500',
  },
  termsText: {
    fontSize: 12,
    color: colors.gray[300],
    textAlign: 'center',
  },
});