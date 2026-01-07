import React, { useState, useEffect } from 'react';

const HackerText = ({ text, className }) => {
    const [displayText, setDisplayText] = useState('');
    // Daftar karakter pengacak
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";

    useEffect(() => {
        let iteration = 0;
        let interval = null;

        interval = setInterval(() => {
            setDisplayText(() => {
                return text
                    .split("")
                    .map((char, index) => {
                        if (index < iteration) {
                            return text[index];
                        }
                        if (char === ' ') return ' ';
                        return chars[Math.floor(Math.random() * chars.length)];
                    })
                    .join("");
            });

            iteration += 0.2;

            if (iteration >= text.length) {
                clearInterval(interval);
                setDisplayText(text);
            }
        }, 30);

        return () => clearInterval(interval);
    }, [text]);

    return (
        <span className={className}>
            {displayText}
        </span>
    );
};

export default HackerText;