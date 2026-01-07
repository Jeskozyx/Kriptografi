import sys
import os
from PIL import Image

# Delimiter unik
DELIMITER = "::STEGO_PESAN_BERAKHIR::"

def data_to_binary(data):
    if isinstance(data, str):
        # Support UTF-8 characters (Emoji, dll)
        return ''.join([format(b, "08b") for b in data.encode('utf-8')])
    return ""

def binary_to_data(binary):
    all_bytes = [binary[i: i+8] for i in range(0, len(binary), 8)]
    byte_array = bytearray()
    for byte in all_bytes:
        if len(byte) == 8:
            try:
                byte_array.append(int(byte, 2))
            except ValueError:
                pass
    try:
        return byte_array.decode('utf-8', errors='ignore')
    except:
        return str(byte_array)

def encode_lsb(image_path, secret_content, output_path, is_file_path=False):
    try:
        # --- LOGIKA BACA FILE ---
        final_message = ""
        if is_file_path and os.path.exists(secret_content):
            # Jika input adalah path file, baca isinya
            with open(secret_content, 'r', encoding='utf-8', errors='ignore') as f:
                final_message = f.read()
        else:
            # Fallback jika string biasa
            final_message = secret_content

        if not final_message:
            print("ERROR: Pesan kosong.")
            return

        img = Image.open(image_path, 'r').convert('RGB')
        width, height = img.size
        new_img = img.copy()
        
        # Tambahkan delimiter
        full_message = final_message + DELIMITER
        
        # Konversi ke binary (UTF-8 supported)
        binary_secret = data_to_binary(full_message)
        
        # Hitung kapasitas maksimal (width * height * 3 channel RGB)
        max_capacity = width * height * 3
        
        if len(binary_secret) > max_capacity:
            print(f"ERROR: Pesan terlalu panjang! Butuh {len(binary_secret)} bit, Kapasitas Gambar: {max_capacity} bit.")
            return

        data_index = 0
        pixels = new_img.load()
        
        for x in range(width):
            for y in range(height):
                if data_index < len(binary_secret):
                    pixel = list(img.getpixel((x, y)))
                    for i in range(3): # R, G, B
                        if data_index < len(binary_secret):
                            # Ubah bit LSB
                            pixel[i] = pixel[i] & ~1 | int(binary_secret[data_index])
                            data_index += 1
                    pixels[x, y] = tuple(pixel)
                else:
                    break
            if data_index >= len(binary_secret):
                break
        
        new_img.save(output_path, "PNG")
        print(f"SUCCESS:{output_path}")

    except Exception as e:
        print(f"ERROR: {e}")

def decode_lsb(image_path):
    try:
        img = Image.open(image_path, 'r').convert('RGB')
        width, height = img.size
        binary_data = ""
        
        # Ekstrak bit LSB
        for x in range(width):
            for y in range(height):
                pixel = img.getpixel((x, y))
                for i in range(3):
                    binary_data += str(pixel[i] & 1)
        
        # Konversi ke text
        decoded_text = binary_to_data(binary_data)
        
        delimiter_pos = decoded_text.find(DELIMITER)
        if delimiter_pos != -1:
            # Print hasil extract. Perhatikan encoding utf-8 untuk terminal
            print(f"EXTRACTED:{decoded_text[:delimiter_pos]}")
        else:
            # Coba cari partial message jika delimiter rusak
            print("ERROR: Tidak ditemukan pesan rahasia (Delimiter tidak cocok).")

    except Exception as e:
        print(f"ERROR: {e}")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("ERROR: Tidak ada argumen.")
        sys.exit(1)

    mode = sys.argv[1]
    
    if mode == 'encode':
        if len(sys.argv) < 5:
            print("ERROR: Argumen encode kurang.")
            sys.exit(1)
            
        image_path = sys.argv[2]
        message_input = sys.argv[3] # Ini sekarang adalah path file temp
        output_dir = sys.argv[4]
        
        if not os.path.exists(output_dir):
            try: os.makedirs(output_dir)
            except: pass

        original_filename = os.path.basename(image_path)
        name_part = os.path.splitext(original_filename)[0]
        output_filename = f"stego_{name_part}.png"
        output_path = os.path.join(output_dir, output_filename)
        
        # Panggil encode dengan flag is_file_path=True karena PHP mengirim path file
        # Kita asumsikan PHP selalu mengirim path file temp
        encode_lsb(image_path, message_input, output_path, is_file_path=True)
        
    elif mode == 'decode':
        if len(sys.argv) < 3:
            print("ERROR: Argumen decode kurang.")
            sys.exit(1)
        image_path = sys.argv[2]
        decode_lsb(image_path)