<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/contact.css" />
  <link rel="icon" type="image/png" href="assets/Favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <script src="js/header.js" defer></script>
  <meta name="description" content="Contact ByteBuy support for product advice, orders, bulk pricing, and tech guidance. We're here to help." />
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- HERO -->
  <section class="contact-hero">
    <div class="contact-hero-overlay">
      <h1>We're Here to Help</h1>
      <p>Fast, friendly support for every tech journey.</p>
    </div>
  </section>

  <!-- QUICK CHANNELS -->
  <section class="contact-channels">
    <div class="channels-grid">
      <a class="channel-card" href="#contactForm" aria-label="Email support">
        <div class="icon">@</div>
        <h3>Email Support</h3>
        <p>Response within 24 hours.</p>
      </a>
      <a class="channel-card" href="#faq" aria-label="Browse FAQs">
        <div class="icon">?</div>
        <h3>Quick Answers</h3>
        <p>Browse FAQs and guides.</p>
      </a>
      <a class="channel-card" href="tel:+18001234567" aria-label="Call us">
        <div class="icon">&#9742;</div>
        <h3>Call Us</h3>
        <p>Mon&ndash;Fri, 9am&ndash;6pm.</p>
      </a>
      <a class="channel-card" href="#location" aria-label="Visit our store">
        <div class="icon">&#128205;</div>
        <h3>Visit Store</h3>
        <p>Get hands-on recommendations.</p>
      </a>
    </div>
  </section>

  <!-- MAIN CONTACT AREA -->
  <main class="contact-main">
    <div class="contact-container">

      <!-- FORM -->
      <section class="contact-form-wrap">
        <h2>Send Us a Message</h2>
        <p class="sub">Average reply time: under 24 hours</p>

        <form id="contactForm" class="contact-form" action="#" method="post" novalidate>
          <div class="form-row">
            <div class="field">
              <label for="fullName">Full Name</label>
              <input id="fullName" name="fullName" type="text" placeholder="Jane Doe" required />
            </div>
            <div class="field">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" placeholder="you@example.com" required />
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label for="topic">Topic</label>
              <select id="topic" name="topic" required>
                <option value="" disabled selected>Select a topic</option>
                <option value="order">Order or Delivery</option>
                <option value="product">Product Advice</option>
                <option value="business">Bulk / Business Pricing</option>
                <option value="budget">Budget Recommendations</option>
                <option value="warranty">Warranty / Returns</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="field">
              <label for="orderId">Order ID <span class="muted">(optional)</span></label>
              <input id="orderId" name="orderId" type="text" placeholder="#BB-12345" />
            </div>
          </div>

          <div class="field stretch-field">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" placeholder="Tell us how we can help..." required></textarea>
          </div>

          <div class="form-options">
            <label class="checkbox">
              <input type="checkbox" name="copy" />
              <span>Send me a copy</span>
            </label>
            <label class="checkbox">
              <input type="checkbox" name="policy" required />
              <span>I agree to the Privacy Policy</span>
            </label>
          </div>

          <button type="submit" class="cta-btn dark-btn">Send Message</button>
        </form>
      </section>

      <!-- PERSONA SIDEBAR -->
      <aside class="contact-aside">
        <h3>Tailored Help</h3>
        <div class="persona-cards">
          <div class="persona-card">
            <h4>Corporate</h4>
            <p>Need bulk pricing or quick quotes? We've got you.</p>
            <a href="#contactForm" class="pill">Request a quote</a>
          </div>
          <div class="persona-card">
            <h4>Budget</h4>
            <p>Looking for reliable, kid-friendly tech on a budget?</p>
            <a href="#contactForm" class="pill">Get budget picks</a>
          </div>
          <div class="persona-card">
            <h4>Professional</h4>
            <p>Warranty, compatibility, or setup questions? We can help.</p>
            <a href="#contactForm" class="pill">Ask a specialist</a>
          </div>
          <div class="persona-card">
            <h4>Student</h4>
            <p>Upgrade advice, performance tips, and deal alerts.</p>
            <a href="#contactForm" class="pill">Get upgrade advice</a>
          </div>
        </div>

        <div class="contact-meta">
          <p><strong>Email:</strong> support@bytebuy.example</p>
          <p><strong>Phone:</strong> +1 (800) 123&ndash;4567</p>
        </div>
      </aside>

    </div>
  </main>

  <!-- FAQ -->
  <section id="faq" class="contact-faq">
    <h2>Frequently Asked Questions</h2>
    <div class="faq-list">
      <details>
        <summary>Where is my order?</summary>
        <p>Most orders ship within 1&ndash;3 business days. Use "Track Order" from the top menu with your Order ID.</p>
      </details>
      <details>
        <summary>Do you offer student or business discounts?</summary>
        <p>Yes&mdash;contact us with your school or company details for tailored pricing.</p>
      </details>
      <details>
        <summary>What&#39;s your return policy?</summary>
        <p>30&#8209;day returns on most items in like-new condition. Some exclusions apply.</p>
      </details>
      <details>
        <summary>Can you recommend a device for my needs?</summary>
        <p>Absolutely&mdash;share your budget and usage, and we'll shortlist great options.</p>
      </details>
    </div>
  </section>

  <!-- LOCATION -->
  <section id="location" class="contact-location">
    <div class="location-card">
      <div class="location-info">
        <h3>Visit Our Store</h3>
        <p>313 Orchard Rd<br>Singapore 238895</p>
        <p class="muted">Mon&ndash;Fri 9:00&ndash;6:00, Sat 10:00&ndash;4:00</p>
      </div>
      <div class="location-map" aria-hidden="true">
        <iframe
          title="ByteBuy Flagship Location"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15955.204108275032!2d103.829!3d1.3039!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da1990b8e47b37%3A0xbdeb1c437ff3be48!2s313%20Orchard%20Road!5e0!3m2!1sen!2ssg!4v1736534400"
          width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>
