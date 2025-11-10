# dekripsi_file.py
import sys
from Crypto.Cipher import AES, Blowfish
from Crypto.Util.Padding import unpad
import hashlib

def derive_keys(key_str):
    h = hashlib.sha256(key_str.encode('utf-8')).digest()
    aes_key = h[:16]
    blow_key = h[:16]
    return aes_key, blow_key

def decrypt_file(input_path, output_path, key_str):
    aes_key, blow_key = derive_keys(key_str)

    with open(input_path, 'rb') as f:
        data = f.read()

    # baca blow_iv (8 byte) + blow_ct
    if len(data) < 8:
        raise ValueError("File terlalu pendek, bukan file yang valid")
    blow_iv = data[:8]
    blow_ct = data[8:]

    # step 1: decrypt Blowfish-CBC -> hasilnya = padded(aes_iv + aes_ct)
    from Crypto.Util.Padding import unpad as unpad_bf
    blow_cipher = Blowfish.new(blow_key, Blowfish.MODE_CBC, blow_iv)
    decrypted_bf = blow_cipher.decrypt(blow_ct)
    # unpad untuk dapatkan (aes_iv + aes_ct)
    try:
        combined = unpad_bf(decrypted_bf, Blowfish.block_size)
    except Exception as e:
        raise ValueError("Blowfish unpad failed: " + str(e))

    # ambil aes_iv (16) + aes_ct
    if len(combined) < 16:
        raise ValueError("Isi hasil blowfish terlalu pendek")
    aes_iv = combined[:16]
    aes_ct = combined[16:]

    # step 2: decrypt AES-CBC
    aes_cipher = AES.new(aes_key, AES.MODE_CBC, aes_iv)
    try:
        plaintext = unpad(aes_cipher.decrypt(aes_ct), AES.block_size)
    except Exception as e:
        raise ValueError("AES unpad failed: " + str(e))

    with open(output_path, 'wb') as f:
        f.write(plaintext)

    print("OK")

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python dekripsi_file.py <input> <output> <key>")
        sys.exit(1)
    try:
        decrypt_file(sys.argv[1], sys.argv[2], sys.argv[3])
    except Exception as e:
        print("Error:", str(e))
        sys.exit(2)
