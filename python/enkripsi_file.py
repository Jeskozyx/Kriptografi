import sys
import os
import hashlib
from Crypto.Cipher import AES, Blowfish
from Crypto.Util.Padding import pad
from Crypto.Random import get_random_bytes

def derive_keys(key_str):
    """
    Membuat kunci 32-byte dari string input menggunakan SHA-256.
    16 byte pertama untuk AES, 16 byte sisanya untuk Blowfish.
    """
    # Pastikan key_str adalah string sebelum encode
    if isinstance(key_str, bytes):
        key_str = key_str.decode('utf-8', errors='ignore')
        
    h = hashlib.sha256(key_str.encode('utf-8')).digest()
    aes_key = h[:16]        # 128-bit key untuk AES
    blow_key = h[:16]       # Key Blowfish (menggunakan byte yang sama agar simpel)
    return aes_key, blow_key

def encrypt_file(input_path, output_path, key_input):
    # --- LOGIKA BARU: Cek apakah key_input adalah file ---
    final_key = ""
    
    # Cek apakah input ketiga itu path file yang valid?
    if os.path.exists(key_input) and os.path.isfile(key_input):
        try:
            # Jika ya, baca isinya sebagai kunci
            with open(key_input, 'r', encoding='utf-8', errors='ignore') as f:
                final_key = f.read()
        except Exception as e:
            print(f"Error membaca file kunci: {e}")
            sys.exit(1)
    else:
        # Jika bukan file, anggap itu string kunci biasa
        final_key = key_input

    if not final_key:
        print("Error: Kunci enkripsi kosong.")
        sys.exit(1)

    # --- PROSES ENKRIPSI ---
    try:
        # 1. Turunkan kunci untuk AES dan Blowfish
        aes_key, blow_key = derive_keys(final_key)

        # 2. Baca file input
        with open(input_path, 'rb') as f:
            data = f.read()

        # 3. Layer 1: AES Encryption
        aes_iv = get_random_bytes(16) # IV AES 16 bytes
        aes_cipher = AES.new(aes_key, AES.MODE_CBC, aes_iv)
        # Pad data agar sesuai blok AES
        aes_ciphertext = aes_cipher.encrypt(pad(data, AES.block_size))

        # 4. Layer 2: Blowfish Encryption
        # Input Blowfish adalah (IV AES + Ciphertext AES)
        data_to_blowfish = aes_iv + aes_ciphertext
        
        blow_iv = get_random_bytes(8) # IV Blowfish 8 bytes
        blow_cipher = Blowfish.new(blow_key, Blowfish.MODE_CBC, blow_iv)
        
        # Pad data untuk Blowfish (karena AES output belum tentu kelipatan 8)
        blow_ciphertext = blow_cipher.encrypt(pad(data_to_blowfish, Blowfish.block_size))

        # 5. Tulis hasil akhir
        # Format File: [IV Blowfish (8)] + [Ciphertext (Blowfish(IV_AES + Cipher_AES))]
        with open(output_path, 'wb') as f:
            f.write(blow_iv + blow_ciphertext)

        # Print "OK" agar PHP tahu proses sukses
        print("OK")

    except Exception as e:
        print(f"Encryption Failed: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    # Argumen: script.py <input> <output> <key_or_keyfile>
    if len(sys.argv) < 4:
        print("Usage: python enkripsi_file.py <input_path> <output_path> <key_string_or_file>")
        sys.exit(1)
        
    encrypt_file(sys.argv[1], sys.argv[2], sys.argv[3])