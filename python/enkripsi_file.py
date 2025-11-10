# enkripsi_file.py
import sys
from Crypto.Cipher import AES, Blowfish
from Crypto.Util.Padding import pad
from Crypto.Random import get_random_bytes
import hashlib
import os

def derive_keys(key_str):
    h = hashlib.sha256(key_str.encode('utf-8')).digest()
    aes_key = h[:16]        # AES-128
    blow_key = h[:16]       # Blowfish key (use same derived bytes)
    return aes_key, blow_key

def encrypt_file(input_path, output_path, key_str):
    aes_key, blow_key = derive_keys(key_str)

    # baca file asli
    with open(input_path, 'rb') as f:
        data = f.read()

    # 1) enkripsi dengan AES-CBC
    aes_iv = get_random_bytes(16)
    aes_cipher = AES.new(aes_key, AES.MODE_CBC, aes_iv)
    aes_ct = aes_cipher.encrypt(pad(data, AES.block_size))

    # gabungkan IV AES + ciphertext AES, lalu enkripsi lagi dengan Blowfish-CBC
    blow_iv = get_random_bytes(8)
    from Crypto.Util.Padding import pad as pad_bf
    # blowfish block size = 8
    blow_cipher = Blowfish.new(blow_key, Blowfish.MODE_CBC, blow_iv)
    # kita enkripsi aes_iv + aes_ct (pad ke multiple of 8)
    to_bf = pad_bf(aes_iv + aes_ct, Blowfish.block_size)
    blow_ct = blow_cipher.encrypt(to_bf)

    # simpan: [blow_iv][aes_iv][blow_ct]? kita simpan [blow_iv][blow_ct] dan aes_iv sudah bagian dari decrypted payload
    # tapi untuk kemudahan kita simpan blow_iv + blow_ct; aes_iv akan diperoleh setelah decrypt blowfish
    with open(output_path, 'wb') as f:
        f.write(blow_iv + blow_ct)

    print("OK")

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python enkripsi_file.py <input> <output> <key>")
        sys.exit(1)
    encrypt_file(sys.argv[1], sys.argv[2], sys.argv[3])
