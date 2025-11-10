import sys
import os
from PIL import Image
import re

# Delimiter (Penanda Akhir Pesan) yang unik
DELIMITER = "::STEGO_PESAN_BERAKHIR::"

# Fungsi untuk mengubah data apapun menjadi string biner
def data_to_binary(data):
    if isinstance(data, str):
        return ''.join([format(ord(i), "08b") for i in data])

# Fungsi untuk mengubah string biner kembali ke teks
def binary_to_data(binary):
    all_bytes = [binary[i: i+8] for i in range(0, len(binary), 8)]
    decoded_data = ""
    for byte in all_bytes:
        if len(byte) == 8:
            try:
                decoded_data += chr(int(byte, 2))
            except ValueError:
                pass # Abaikan bit sisa yang tidak membentuk byte penuh
    return decoded_data

# Fungsi untuk menyembunyikan data di LSB
def encode_lsb(image_path, secret_message, output_path):
    try:
        # Buka gambar dan pastikan dalam mode RGB
        img = Image.open(image_path, 'r').convert('RGB')
        width, height = img.size
        new_img = img.copy()
        
        # Tambahkan delimiter ke pesan
        secret_message += DELIMITER
        binary_secret = data_to_binary(secret_message)
        
        data_index = 0
        num_pixels = width * height
        
        if len(binary_secret) > num_pixels * 3:
            # *3 karena kita pakai 3 channel (R, G, B)
            print("ERROR: Pesan terlalu besar untuk gambar ini.")
            return

        pixels = new_img.load()

        for x in range(width):
            for y in range(height):
                if data_index < len(binary_secret):
                    # Ambil data piksel (R, G, B)
                    pixel = list(img.getpixel((x, y)))
                    
                    for i in range(3): # Loop R, G, B
                        if data_index < len(binary_secret):
                            # Ganti LSB piksel dengan bit pesan
                            pixel[i] = pixel[i] & ~1 | int(binary_secret[data_index])
                            data_index += 1
                            
                    # Set piksel baru
                    pixels[x, y] = tuple(pixel)
                else:
                    # Selesai menyisipkan
                    break
            if data_index >= len(binary_secret):
                break
        
        # Simpan sebagai PNG agar LSB tidak hilang (lossless)
        new_img.save(output_path, "PNG")
        print(f"SUCCESS:{output_path}") # Kirim path sukses ke PHP

    except Exception as e:
        print(f"ERROR: {e}")

# Fungsi untuk mengekstrak data dari LSB
def decode_lsb(image_path):
    try:
        img = Image.open(image_path, 'r').convert('RGB')
        width, height = img.size
        binary_data = ""
        
        for x in range(width):
            for y in range(height):
                pixel = img.getpixel((x, y))
                
                for i in range(3): # Loop R, G, B
                    # Ekstrak LSB (bit terakhir)
                    binary_data += str(pixel[i] & 1)
        
        # Ubah data biner ke teks
        decoded_text = binary_to_data(binary_data)
        
        # Cari delimiter kita
        delimiter_pos = decoded_text.find(DELIMITER)
        if delimiter_pos != -1:
            # Jika ditemukan, print pesan sebelum delimiter
            print(f"EXTRACTED:{decoded_text[:delimiter_pos]}")
        else:
            print("ERROR: Tidak ada pesan rahasia ditemukan (delimiter tidak ada).")

    except Exception as e:
        print(f"ERROR: {e}")

# --- MAIN ---
if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("ERROR: Argumen tidak cukup.")
        sys.exit(1)

    mode = sys.argv[1]
    image_path = sys.argv[2]
    
    if mode == 'encode':
        if len(sys.argv) < 5:
            print("ERROR: Argumen encode tidak lengkap.")
            sys.exit(1)
        secret_message = sys.argv[3]
        output_dir = sys.argv[4]
        
        # Buat nama file output
        original_filename = os.path.basename(image_path).split('.')[0]
        output_filename = f"stego_{original_filename}.png"
        output_path = os.path.join(output_dir, output_filename)
        
        encode_lsb(image_path, secret_message, output_path)
        
    elif mode == 'decode':
        decode_lsb(image_path)
    else:
        print("ERROR: Mode tidak valid. Gunakan 'encode' atau 'decode'.")
        sys.exit(1)