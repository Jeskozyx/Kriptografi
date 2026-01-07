# dekripsi_file.py
import sys
import os
import hashlib
from Crypto.Cipher import AES, Blowfish
from Crypto.Util.Padding import unpad

def derive_keys(key_str):
    if isinstance(key_str, bytes):
        key_str = key_str.decode('utf-8', errors='ignore')
        
    h = hashlib.sha256(key_str.encode('utf-8')).digest()
    aes_key = h[:16]
    blow_key = h[:16]
    return aes_key, blow_key

def decrypt_file(input_path, output_path, key_input):
    # --- LOGIKA BARU: Cek apakah input kunci berupa file ---
    final_key = ""
    
    # Cek path file valid
    if os.path.exists(key_input) and os.path.isfile(key_input):
        try:
            with open(key_input, 'r', encoding='utf-8', errors='ignore') as f:
                final_key = f.read()
        except Exception as e:
            print(f"Error membaca file kunci: {e}")
            sys.exit(1)
    else:
        # Fallback: anggap string biasa
        final_key = key_input

    if not final_key:
        print("Error: Kunci kosong.")
        sys.exit(1)
    
    # --- PROSES DEKRIPSI ---
    aes_key, blow_key = derive_keys(final_key)
    
    with open(input_path, 'rb') as f:
        data = f.read()
        
    if len(data) < 8:
        raise ValueError("File terlalu pendek, bukan file yang valid")
        
    # 1. Layer Blowfish
    blow_iv = data[:8]
    blow_ct = data[8:]
    
    blow_cipher = Blowfish.new(blow_key, Blowfish.MODE_CBC, blow_iv)
    decrypted_bf = blow_cipher.decrypt(blow_ct)
    
    try:
        from Crypto.Util.Padding import unpad as unpad_bf
        combined = unpad_bf(decrypted_bf, Blowfish.block_size)
    except Exception as e:
        raise ValueError("Blowfish unpad failed (Kunci salah atau file rusak): " + str(e))
        
    if len(combined) < 16:
        raise ValueError("Isi hasil blowfish terlalu pendek")
        
    # 2. Layer AES
    aes_iv = combined[:16]
    aes_ct = combined[16:]
    
    aes_cipher = AES.new(aes_key, AES.MODE_CBC, aes_iv)
    try:
        plaintext = unpad(aes_cipher.decrypt(aes_ct), AES.block_size)
    except Exception as e:
        raise ValueError("AES unpad failed: " + str(e))
        
    # Tulis hasil
    with open(output_path, 'wb') as f:
        f.write(plaintext)
        
    print("OK")

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python dekripsi_file.py <input_path> <output_path> <key_file_or_string>")
        sys.exit(1)
        
    try:
        decrypt_file(sys.argv[1], sys.argv[2], sys.argv[3])
    except Exception as e:
        print("Error:", str(e))
        sys.exit(2)