<?php
    $whatsapp = "https://wa.me/+629697053591";
    $email = "mailto:scover@gmail.com";
    $gallery = [
        "gallery1.jpg",
        "gallery2.jpg",
        "gallery3.jpg",
        "gallery4.jpg",
    ];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <link rel="stylesheet" href="css/kontak.css">

</head>
<body>
    <div class="container">
        <header>
            <h1>Tentang Kami</h1>
        </header>
        <section class="contact">
            <a href="<?php echo $whatsapp; ?>" class="btn">WhatsApp</a>
            <a href="<?php echo $email; ?>" class="btn">Email</a>
        </section>
        <section class="gallery">
            <h2>Galeri</h2>
            <div class="gallery-grid">
                <?php foreach ($gallery as $img) : ?>
                    <div class="gallery-item">
                        <img src="<?php echo $img; ?>" alt="Gallery Image">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
</html>
