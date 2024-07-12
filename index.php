<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30, '/');
   header('location:index.php');
}

if (isset($_POST['check'])) {

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if ($total_rooms >= 30) {
      $warning_msg[] = 'rooms are not available';
   } else {
      $success_msg[] = 'rooms are available';
   }

}

if (isset($_POST['book'])) {

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
      $total_rooms += $fetch_bookings['rooms'];
   }

   if ($total_rooms >= 30) {
      $warning_msg[] = 'rooms are not available';
   } else {

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if ($verify_bookings->rowCount() > 0) {
         $warning_msg[] = 'room booked alredy!';
      } else {
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if (isset($_POST['send'])) {

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if ($verify_message->rowCount() > 0) {
      $warning_msg[] = 'message sent already!';
   } else {
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>MORNING PERSON HOTEL</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- home section starts  -->

   <section class="home" id="home">

      <div class="swiper home-slider">

         <div class="swiper-wrapper">

            <div class="box swiper-slide">
               <img src="images/home-img-1.jpg" alt="">
               <div class="flex">
                  <h3>Luxurious rooms</h3>
                  <a href="#availability" class="btn">check availability</a>
               </div>
            </div>

            <div class="box swiper-slide">
               <img src="images/home-img-2.jpg" alt="">
               <div class="flex">
                  <h3>Foods and drinks</h3>
                  <a href="#reservation" class="btn">make a reservation</a>
               </div>
            </div>

            <div class="box swiper-slide">
               <img src="images/home-img-3.jpg" alt="">
               <div class="flex">
                  <h3>Luxurious halls</h3>
                  <a href="#contact" class="btn">contact us</a>
               </div>
            </div>

         </div>

         <div class="swiper-button-next"></div>
         <div class="swiper-button-prev"></div>

      </div>

   </section>

   <!-- home section ends -->

   <!-- availability section starts  -->

   <section class="availability" id="availability">

      <form action="" method="post">
         <div class="flex">
            <div class="box">
               <p>Check in <span>*</span></p>
               <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
               <p>Check out <span>*</span></p>
               <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
               <p>adults <span>*</span></p>
               <select name="adults" class="input" required>
                  <option value="1">1 adult</option>
                  <option value="2">2 adults</option>
                  <option value="3">3 adults</option>
                  <option value="4">4 adults</option>
                  <option value="5">5 adults</option>
                  <option value="6">6 adults</option>
               </select>
            </div>
            <div class="box">
               <p>Childs <span>*</span></p>
               <select name="childs" class="input" required>
                  <option value="-">0 child</option>
                  <option value="1">1 child</option>
                  <option value="2">2 childs</option>
                  <option value="3">3 childs</option>
                  <option value="4">4 childs</option>
                  <option value="5">5 childs</option>
                  <option value="6">6 childs</option>
               </select>
            </div>
            <div class="box">
               <p>Rooms <span>*</span></p>
               <select name="rooms" class="input" required>
                  <option value="1">1 room</option>
                  <option value="2">2 rooms</option>
                  <option value="3">3 rooms</option>
                  <option value="4">4 rooms</option>
                  <option value="5">5 rooms</option>
                  <option value="6">6 rooms</option>
               </select>
            </div>
         </div>
         <input type="submit" value="check availability" name="check" class="btn">
      </form>

   </section>

   <!-- availability section ends -->

   <!-- about section starts  -->

   <section class="about" id="about">

      <div class="row">
         <div class="image">
            <img src="images/about-img-1.jpg" alt="">
         </div>
         <div class="content">
            <h3>Best staff</h3>
            <p>this is the best staff of our hotel</p>
            <a href="#reservation" class="btn">make a reservation</a>
         </div>
      </div>

      <div class="row revers">
         <div class="image">
            <img src="images/about-img-2.jpg" alt="">
         </div>
         <div class="content">
            <h3>Best foods</h3>
            <p>At Our Hotel, we invite you to experience our renowned Best Foods dining facility, where culinary
               excellence meets exceptional service. Our commitment to quality and creativity ensures every dish is a
               masterpiece, crafted to delight even the most discerning palate.</p>
            <a href="#contact" class="btn">contact us</a>
         </div>
      </div>

      <div class="row">
         <div class="image">
            <img src="images/about-img-3.jpg" alt="">
         </div>
         <div class="content">
            <h3>Swimming pool</h3>
            <p>We adhere to strict cleanliness and sanitation protocols, providing a clean and hygienic pool area for
               your enjoyment.</p>
            <a href="#availability" class="btn">check availability</a>
         </div>
      </div>

   </section>

   <!-- about section ends -->

   <!-- services section starts  -->

   <section class="services">

      <div class="box-container">

         <div class="box">
            <img src="images/icon-1.png" alt="">
            <h3>Food & drinks</h3>
            <p>Indulge in a culinary journey at our hotel, where we offer an array of exquisite dining options to suit
               every palate. Our expert chefs use only the freshest ingredients to create delicious and visually
               stunning dishes that will tantalize your taste buds.</p>
         </div>

         <div class="box">
            <img src="images/icon-2.png" alt="">
            <h3>Outdoor dining</h3>
            <p>Set the stage for romance with a candlelit dinner under the stars. Our outdoor dining area provides the
               perfect backdrop for an intimate evening, complete with exquisite cuisine and exceptional service.</p>
         </div>

         <div class="box">
            <img src="images/icon-3.png" alt="">
            <h3>Beach view</h3>
            <p>Wake up to the soothing sound of waves and the sight of the sun rising over the horizon. Our beach view
               rooms are designed to maximize your connection to nature, with large windows and private balconies that
               offer panoramic views of the beach and ocean.</p>
         </div>

         <div class="box">
            <img src="images/icon-4.png" alt="">
            <h3>Decorations</h3>
            <p>LWith years of experience in event design, our decorators bring creativity and innovation to every
               project. We stay updated with the latest trends in decor to offer you cutting-edge designs that are both
               timeless and sophisticated.</p>
         </div>

         <div class="box">
            <img src="images/icon-5.png" alt="">
            <h3>Swimming pool</h3>
            <p>Dive into relaxation at our luxurious swimming pool, where crystal-clear waters and serene surroundings
               await. Whether you’re looking to unwind after a long day of sightseeing or simply soak up the sun, our
               pool area offers the perfect oasis for guests of all ages.</p>
         </div>

         <div class="box">
            <img src="images/icon-6.png" alt="">
            <h3>Resort beach</h3>
            <p>Create lasting memories with your loved ones with our family-friendly beach activities. Build
               sandcastles, play beach volleyball, or join our organized family games and events. Our resort beach is
               designed to cater to guests of all ages.</p>
         </div>

      </div>

   </section>

   <!-- services section ends -->

   <!-- reservation section starts  -->

   <section class="reservation" id="reservation">

      <form action="" method="post">
         <h3>Make a reservation</h3>
         <div class="flex">
            <div class="box">
               <p>Your name <span>*</span></p>
               <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
            </div>
            <div class="box">
               <p>Your email <span>*</span></p>
               <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
            </div>
            <div class="box">
               <p>Your number <span>*</span></p>
               <input type="number" name="number" maxlength="10" min="0" max="9999999999" required
                  placeholder="enter your number" class="input">
            </div>
            <div class="box">
               <p>Rooms <span>*</span></p>
               <select name="rooms" class="input" required>
                  <option value="1" selected>1 room</option>
                  <option value="2">2 rooms</option>
                  <option value="3">3 rooms</option>
                  <option value="4">4 rooms</option>
                  <option value="5">5 rooms</option>
                  <option value="6">6 rooms</option>
               </select>
            </div>
            <div class="box">
               <p>Check in <span>*</span></p>
               <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
               <p>Check out <span>*</span></p>
               <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
               <p>Adults <span>*</span></p>
               <select name="adults" class="input" required>
                  <option value="1" selected>1 adult</option>
                  <option value="2">2 adults</option>
                  <option value="3">3 adults</option>
                  <option value="4">4 adults</option>
                  <option value="5">5 adults</option>
                  <option value="6">6 adults</option>
               </select>
            </div>
            <div class="box">
               <p>Childs <span>*</span></p>
               <select name="childs" class="input" required>
                  <option value="0" selected>0 child</option>
                  <option value="1">1 child</option>
                  <option value="2">2 childs</option>
                  <option value="3">3 childs</option>
                  <option value="4">4 childs</option>
                  <option value="5">5 childs</option>
                  <option value="6">6 childs</option>
               </select>
            </div>
         </div>
         <input type="submit" value="book now" name="book" class="btn">
      </form>

   </section>

   <!-- reservation section ends -->

   <!-- gallery section starts  -->

   <section class="gallery" id="gallery">

      <div class="swiper gallery-slider">
         <div class="swiper-wrapper">
            <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-2.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-3.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-4.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-5.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-6.jpg" class="swiper-slide" alt="">
         </div>
         <div class="swiper-pagination"></div>
      </div>

   </section>

   <!-- gallery section ends -->

   <!-- contact section starts  -->

   <section class="contact" id="contact">

      <div class="row">

         <form action="" method="post">
            <h3>Send us message</h3>
            <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
            <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
            <input type="number" name="number" required maxlength="10" min="0" max="9999999999"
               placeholder="enter your number" class="box">
            <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30"
               rows="10"></textarea>
            <input type="submit" value="send message" name="send" class="btn">
         </form>

         <div class="faq">
            <h3 class="title">Frequently asked questions</h3>
            <div class="box active">
               <h3>how to cancel?</h3>
               <p>1. Click on the “My Orders” or “Order History” menu to see a list of your orders.
               </p>
               <p>2. Find the booking you want to cancel from your booking list and click on details
                  the order.</p>
               <p>3. On the order details page, you will see the option to cancel the order. Click the button
                  “Cancel Order”.</p>
               <p>4. After clicking the “Cancel Booking” button, you will be asked to confirm the cancellation.
                  Make sure all information is correct before confirming.</p>
               <p>5.After successfully canceling your order, you will receive a notification stating that
                  Your order has been cancelled.</p>
            </div>
            <div class="box">
               <h3>Is there any vacancy?</h3>
               <p>Nothing</p>
            </div>
            <div class="box">
               <h3>What are payment methods?</h3>
               <p>Direct payments and online payments</p>
            </div>
            <div class="box">
               <h3>How to claim coupons codes?</h3>
               <p>Tell the receptionist</p>
            </div>
            <div class="box">
               <h3>What are the age requirements?</h3>
               <p>22-80</p>
            </div>
         </div>

      </div>

   </section>

   <!-- contact section ends -->

   <!-- reviews section starts  -->

   <section class="reviews" id="reviews">

      <div class="swiper reviews-slider">

         <div class="swiper-wrapper">
            <div class="swiper-slide box">
               <img src="images/pic-1.jpg" alt="">
               <h3>Zayn malik</h3>
               <p>I recently stayed at Morning Person Hotel and had an absolutely fantastic experience. The staff was
                  incredibly
                  friendly and accommodating, and the room was clean, spacious, and well-appointed. The location is
                  perfect, close to major attractions and public transportation. The breakfast buffet was delicious with
                  a great variety of options. I highly recommend this hotel to anyone visiting the area. I will
                  definitely stay here again!.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-2.jpg" alt="">
               <h3>Elizabeth</h3>
               <p>From the moment we arrived at Morning Person Hotel, we were treated like royalty. The check-in process
                  was
                  smooth and the staff went out of their way to ensure we had everything we needed. Our room was
                  immaculate and the bed was extremely comfortable. The hotel's amenities, including the pool and spa,
                  were top-notch. We especially loved the complimentary afternoon tea. This hotel truly made our
                  vacation special.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-3.png" alt="">
               <h3>Ana de armas</h3>
               <p>My stay at Morning Person Hotel was quite disappointing. The room was outdated and not as clean as I
                  expected.
                  There was a lot of noise from the street, which made it difficult to sleep. The staff seemed
                  disinterested and were not very helpful when we had questions. The breakfast options were limited and
                  not very appetizing. Overall, I would not recommend this hotel and will not be staying here again.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-4.png" alt="">
               <h3>Limbat</h3>
               <p>I had high hopes for Morning Person Hotel, but unfortunately, my experience did not live up to my
                  expectations. The check-in process was slow and the receptionist was rude. The room was smaller than
                  advertised and had a musty smell. We also encountered issues with the Wi-Fi connectivity. When we
                  brought up our concerns, the staff was dismissive and unresponsive. This was definitely not worth the
                  price we paid.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-5.png" alt="">
               <h3>Hj.Bolot</h3>
               <p>Our stay at Morning Person Hotel was average. The location is great, close to many shops and
                  restaurants. The
                  room was decent but could use some updates. The bed was comfortable, but the pillows were too flat.
                  The breakfast was okay, but nothing special. The staff was polite but not overly friendly. It was an
                  okay experience, but I might explore other options next time.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-6.png" alt="">
               <h3>Pinka</h3>
               <p>I have mixed feelings about my stay at Morning Person On the positive side, the hotel is in a
                  convenient location and the lobby is beautifully decorated. However, the room we stayed in had several
                  issues, including a leaking faucet and poor lighting. The staff was courteous, but the service was
                  slow at times. While there were some good aspects, there is definitely room for improvement.</p>
            </div>
         </div>

         <div class="swiper-pagination"></div>
      </div>

   </section>

   <!-- reviews section ends  -->





   <?php include 'components/footer.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>

</html>