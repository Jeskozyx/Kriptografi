import React, { useState } from 'react'
import ReactDOM from 'react-dom/client'
import LetterGlitch from '@/components/LetterGlitch.jsx'
import SplashScreen from '@/components/SplashScreen.jsx'
import ScrollVelocity from '@/components/ScrollVelocity.jsx'
import './index.css'

/* =========================================
   1. SCROLL VELOCITY (Teks Berjalan)
   ========================================= */

// --- Velocity Bagian ATAS ---
const velocityTop = document.getElementById('react-velocity-top');
if (velocityTop) {
  ReactDOM.createRoot(velocityTop).render(
    <React.StrictMode>
      <ScrollVelocity
        texts={['Sistem Keamanan Data Modern • Kriptografi & Steganografi • ']}
        velocity={30}
        className="text-lg font-bold tracking-wider uppercase font-mono"
      />
    </React.StrictMode>
  )
}

// --- Velocity Bagian BAWAH ---
const velocityBottom = document.getElementById('react-velocity-bottom');
if (velocityBottom) {
  ReactDOM.createRoot(velocityBottom).render(
    <React.StrictMode>
      <ScrollVelocity
        texts={['Rail Fence • XOR • AES • Blowfish • IDEA • LSB • Salsa20 • ']}
        velocity={-30} // Minus agar arahnya berlawanan (Kanan ke Kiri)
        className="text-base font-mono opacity-80"
      />
    </React.StrictMode>
  )
}

/* =========================================
   2. BACKGROUND ANIMATION (Letter Glitch)
   ========================================= */

const renderBackground = (elementId, glitchConfig) => {
  const rootElement = document.getElementById(elementId);

  if (rootElement) {
    ReactDOM.createRoot(rootElement).render(
      <React.StrictMode>
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          zIndex: -1,
          backgroundColor: '#111827' // bg-gray-900
        }}>
          <LetterGlitch
            glitchSpeed={50}
            centerVignette={true}
            outerVignette={false}
            smooth={true}
            // Default colors (bisa ditimpa config)
            glitchColors={['#2b4539', '#61dca3', '#61b3dc']}
            {...glitchConfig}
          />
        </div>
      </React.StrictMode>
    )
  }
}

// Render untuk Halaman LOGIN (Nuansa Hijau/Biru)
renderBackground('react-background-login', {
  glitchColors: ['#2b4539', '#61dca3', '#61b3dc']
});

// Render untuk Halaman REGISTER (Nuansa Ungu/Pink)
renderBackground('react-background-register', {
  glitchColors: ['#4a0e4e', '#813391', '#c686d1']
});


/* =========================================
   3. SPLASH SCREEN (Hacker Intro)
   ========================================= */

const splashElement = document.getElementById('react-splash-screen');

if (splashElement) {
  // Ambil username dari PHP (data-username)
  const username = splashElement.dataset.username || "Pengguna";

  // Wrapper component untuk menangani state unmount
  const SplashWrapper = () => {
    const [show, setShow] = useState(true);

    // Jika show false, hapus dari DOM (return null)
    if (!show) return null;

    return (
      <SplashScreen
        username={username}
        onFinish={() => setShow(false)}
      />
    );
  }

  ReactDOM.createRoot(splashElement).render(
    <React.StrictMode>
      <SplashWrapper />
    </React.StrictMode>
  )
}