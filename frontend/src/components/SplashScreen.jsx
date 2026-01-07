import React, { useEffect, useState } from 'react'
import HackerText from '@/components/HackerText'

const SplashScreen = ({ username, onFinish }) => {
    const [isVisible, setIsVisible] = useState(true)

    useEffect(() => {
        const timer = setTimeout(() => {
            setIsVisible(false)
            setTimeout(() => {
                onFinish()
            }, 1000)
        }, 4500)

        return () => clearTimeout(timer)
    }, [onFinish])

    return (
        <div className={`fixed inset-0 z-50 flex flex-col items-center justify-center bg-gray-900 transition-opacity duration-1000 ${isVisible ? 'opacity-100' : 'opacity-0 pointer-events-none'}`}>
            <div className="text-center px-4">

                <p className="text-green-500 font-mono text-sm mb-4 tracking-[0.2em] uppercase animate-pulse">
                    &gt; SYSTEM_INITIALIZED...
                </p>
                <h1 className="text-3xl md:text-5xl font-bold tracking-wide text-white leading-tight">
                    <HackerText
                        text={`Selamat Datang, ${username}`}
                        className="font-mono text-white"
                    />
                </h1>

                <div className="w-48 h-1 bg-gray-800 rounded-full mt-8 mx-auto overflow-hidden border border-gray-700">
                    <div className="h-full bg-green-500 animate-[loading_4s_ease-in-out_forwards]" style={{ width: '0%' }}></div>
                </div>
            </div>

            <style>{`
                .font-mono {
                    font-family: 'Courier New', Courier, monospace;
                    text-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
                }
                @keyframes loading {
                    0% { width: 0%; }
                    10% { width: 5%; }
                    50% { width: 60%; }
                    100% { width: 100%; }
                }
            `}</style>
        </div>
    )
}

export default SplashScreen