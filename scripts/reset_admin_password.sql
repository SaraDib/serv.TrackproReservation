-- Safely reset admin@trackpro.com's password to bcrypt('12345678')
UPDATE users
SET password = '$2y$10$fNfGCwhXwnMrQdt.oedGNuClos41WM90688g/KS/KPQjl.Qae97FW'
WHERE email = 'admin@trackpro.com';