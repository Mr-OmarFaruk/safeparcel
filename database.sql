CREATE DATABASE IF NOT EXISTS safeparcel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safeparcel;

DROP TABLE IF EXISTS parcel_activity;
DROP TABLE IF EXISTS parcels;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  mobile VARCHAR(30) DEFAULT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE parcels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parcel_serial VARCHAR(60) NOT NULL UNIQUE,
  sender_name VARCHAR(120) NOT NULL,
  sender_mobile VARCHAR(30) NOT NULL,
  sender_location VARCHAR(180) NOT NULL,
  product_description TEXT NOT NULL,
  destination_address TEXT NOT NULL,
  weight_kg DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  sender_remarks TEXT DEFAULT NULL,
  receiver_name VARCHAR(120) NOT NULL,
  receiver_mobile VARCHAR(30) NOT NULL,
  receiver_address TEXT NOT NULL,
  bill_per_kg DECIMAL(10,2) NOT NULL DEFAULT 100.00,
  bill_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  receiver_remarks TEXT DEFAULT NULL,
  current_status ENUM('Booked', 'In Transit', 'Out for Delivery', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Booked',
  created_by INT DEFAULT NULL,
  updated_by INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_parcels_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_parcels_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE parcel_activity (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parcel_id INT NOT NULL,
  user_id INT DEFAULT NULL,
  activity VARCHAR(220) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_activity_parcel FOREIGN KEY (parcel_id) REFERENCES parcels(id) ON DELETE CASCADE,
  CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO users (name, email, mobile, role, password) VALUES
('SafeParcel Admin', 'admin@safeparcel.test', '01700000001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCibKELFAM5h0M6YpG'),
('SafeParcel User', 'user@safeparcel.test', '01700000002', 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCibKELFAM5h0M6YpG');

INSERT INTO parcels (
  parcel_serial, sender_name, sender_mobile, sender_location, product_description,
  destination_address, weight_kg, sender_remarks, receiver_name, receiver_mobile,
  receiver_address, bill_per_kg, bill_amount, receiver_remarks, current_status, created_by
) VALUES
('SP-2026-0001', 'Rahim Uddin', '01811111111', 'Dhaka', 'Electronics accessories', 'Chattogram Sadar, Chattogram', 2.50, 'Handle with care', 'Karim Ahmed', '01922222222', 'Chattogram Sadar, Chattogram', 100.00, 250.00, 'Call before delivery', 'In Transit', 1),
('SP-2026-0002', 'Nusrat Jahan', '01833333333', 'Sylhet', 'Books and documents', 'Mirpur, Dhaka', 1.20, 'Documents inside', 'Mitu Akter', '01944444444', 'Mirpur-10, Dhaka', 100.00, 120.00, '', 'Booked', 2);

INSERT INTO parcel_activity (parcel_id, user_id, activity) VALUES
(1, 1, 'Parcel booked and moved to In Transit'),
(2, 2, 'Parcel booked');
