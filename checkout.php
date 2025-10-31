<?php
// --- PAYU INTEGRATION CONFIGURATION ---
$MERCHANT_KEY = "GnVkSn"; 
$MERCHANT_SALT = "R7qVqGqCjlKqzbSIHDFLovL0TSnmAw4Zy";
$PAYU_BASE_URL = "https://secure.payu.in"; // Live URL
$SUCCESS_URL = "https://zerofundedhustle.com/success";
$FAILURE_URL = "https://zerofundedhustle.com/failure";

// Product Details
$productinfo = "ESCAPE THE 9-5 | ZEROFUNDEDHUSTLE";
$amount = "299.00";

$action = '';
$hash_string = '';
$hash = '';
$txnid = '';

// Check if the form has been submitted
if(isset($_POST['firstname']) && isset($_POST['email'])) {
    
    // --- Form has been submitted, now generate hash and prepare for PayU ---

    // Sanitize user input
    $firstname = htmlspecialchars($_POST['firstname']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']); // Optional, but good to have
    
    // Generate a unique transaction ID
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    
    // Create the hash string *with* the user's data
    $hash_string = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' .
                   $firstname . '|' . $email . '|' .
                   '' . '|' . '' . '|' . '' . '|' . '' . '|' . '' . '|' . '' . '|' . '' . '|' . '' . '|' . '' . '|' .
                   $MERCHANT_SALT;

    // Generate the secure SHA-512 hash
    $hash = hash('sha512', $hash_string);
    
    // Set the PayU action URL
    $action = $PAYU_BASE_URL . '/_payment';

    // Set the page to auto-submit to PayU
    $onloadScript = 'onload="submitPayuForm()"';

} else {
    // --- Form has NOT been submitted, just display the checkout page ---
    
    // Set variables to empty
    $firstname = "";
    $email = "";
    $phone = "";
    $txnid = "";
    $hash = "";
    $action = ""; // Submit to self
    $onloadScript = ""; // Do not auto-submit
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ZeroFundedHustle</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #010409; color: #E6EDF3; }
        .accent-color { color: #238636; }
        .bg-accent { background-color: #238636; }
        .hover-bg-accent:hover { background-color: #2EA043; }
        .border-accent { border-color: #238636; }
        .ring-accent:focus { --tw-ring-color: #238636; }
        .form-input { background-color: #0D1117; border: 1px solid #30363d; color: #E6EDF3; border-radius: 6px; }
        .form-input:focus { background-color: #010409; border-color: #238636; box-shadow: 0 0 0 3px rgba(35, 134, 54, 0.3); outline: none; }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-container { transition: transform 0.3s ease, opacity 0.3s ease; }
        .modal-content::-webkit-scrollbar { width: 8px; }
        .modal-content::-webkit-scrollbar-track { background: #0D1117; }
        .modal-content::-webkit-scrollbar-thumb { background-color: #30363d; border-radius: 4px; border: 2px solid #0D1117; }
        .modal-content::-webkit-scrollbar-thumb:hover { background-color: #238636; }
    </style>
    
    <?php if(!empty($action)): ?>
    <!-- This script is only included if the form has been submitted and we're ready to redirect to PayU -->
    <script>
        function submitPayuForm() {
            var payuForm = document.forms.payuForm;
            payuForm.submit();
        }
    </script>
    <?php endif; ?>
</head>
<!-- The onloadScript will either be empty or will call submitPayuForm() -->
<body class="antialiased" <?php echo $onloadScript; ?>>

    <!-- Header -->
    <header class="bg-black/30 backdrop-blur-lg sticky top-0 z-50 border-b border-gray-800">
        <nav class="container mx-auto px-6 py-4 flex justify-center items-center">
            <a href="/" class="text-2xl font-extrabold tracking-tight text-gray-200">zero<span class="accent-color">funded</span>hustle</a>
        </nav>
    </header>
    
    <?php if(!empty($action)): ?>
    <!-- 
      STEP 2: AUTO-SUBMITTING TO PAYU
      If $action is set, it means the user just submitted the form.
      The PHP has generated the hash. We now show a hidden form
      and auto-submit it to PayU. The user sees a loading message.
    -->
    <main class="py-24 md:py-32">
        <div class="container mx-auto px-6 max-w-lg text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-200 mb-6">Connecting to Payment Gateway...</h1>
            <p class="text-lg text-gray-400 mb-8">Please wait, you are being securely redirected to PayU. Do not refresh or press back.</p>
            <!-- Loading Spinner -->
            <svg class="animate-spin h-10 w-10 text-accent-color mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        <!-- This is the hidden form that gets auto-submitted to PayU -->
        <form action="<?php echo $action; ?>" method="post" name="payuForm" class="hidden">
            <!-- Merchant and Transaction Details -->
            <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY; ?>" />
            <input type="hidden" name="hash" value="<?php echo $hash; ?>" />
            <input type="hidden" name="txnid" value="<?php echo $txnid; ?>" />
            
            <!-- Product and Amount Details -->
            <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
            <input type="hidden" name="productinfo" value="<?php echo $productinfo; ?>" />
            
            <!-- User Details -->
            <input type="hidden" name="firstname" value="<?php echo $firstname; ?>" />
            <input type="hidden" name="email" value="<?php echo $email; ?>" />
            <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
            
            <!-- URL Redirects -->
            <input type="hidden" name="surl" value="<?php echo $SUCCESS_URL; ?>" />
            <input type="hidden" name="furl" value="<?php echo $FAILURE_URL; ?>" />
            
            <!-- Optional PayU Fields -->
            <input type="hidden" name="service_provider" value="payu_paisa" />
            <input type="hidden" name="lastname" value="" />
            <input type="hidden" name="address1" value="" />
            <input type="hidden" name="address2" value="" />
            <input type="hidden" name="city" value="" />
            <input type="hidden" name="state" value="" />
            <input type="hidden" name="country" value="" />
            <input type="hidden" name="zipcode" value="" />
        </form>
    
    <?php else: ?>
    <!-- 
      STEP 1: SHOW CHECKOUT FORM
      If $action is not set, it means the user is visiting the page
      for the first time. We show them the form to enter their details.
    -->
    <main class="py-24 md:py-32">
        <div class="container mx-auto px-6 max-w-lg">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-200 mb-6 text-center">Secure Checkout</h1>
            
            <!-- Order Summary -->
            <div class="bg-[#0D1117] border border-gray-800 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-200 mb-4">Order Summary</h2>
                <div class="flex justify-between items-center text-gray-300">
                    <p class="font-medium">ESCAPE THE 9-5 | ZEROFUNDEDHUSTLE</p>
                    <p class="font-bold text-lg">₹299.00</p>
                </div>
                <hr class="border-gray-700 my-4">
                <div class="flex justify-between items-center text-gray-200 font-bold text-xl">
                    <p>Total</p>
                    <p>₹299.00</p>
                </div>
            </div>
            
            <!-- 
              This form submits to itself (action="").
              The PHP logic at the top will catch the POST data.
            -->
            <form action="" method="POST" name="checkoutForm">
                <div class="space-y-6">
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-input w-full p-3" placeholder="Enter your full name" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" class="form-input w-full p-3" placeholder="you@example.com" required>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Phone Number (Optional)</label>
                        <input type="text" name="phone" id="phone" class="form-input w-full p-3" placeholder="Optional, for payment updates">
                    </div>
                </div>
                
                <div class="mt-8">
                    <button type="submit" class="w-full bg-accent text-white font-bold py-4 px-6 rounded-md hover-bg-accent transition-colors text-lg">
                        Proceed to Pay ₹299.00
                    </button>
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-xs text-gray-500">All payments are secure and encrypted. Processed by PayU.</p>
                </div>
            </form>
        </div>
    </main>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="mt-24 py-12 border-t border-gray-800">
        <div class="container mx-auto px-6 text-center text-gray-500">
            <div class="mb-6">
                <a href="https://www.instagram.com/zerofundedhustle" target="_blank" class="mx-3 hover:text-green-400 transition-colors">Instagram</a>
            </div>
            <div class="mb-6 text-sm">
                 <a id="privacy-policy-link" href="#" class="mx-3 hover:text-green-400 transition-colors">Privacy Policy</a>
                 <a id="terms-of-use-link" href="#" class="mx-3 hover:text-green-400 transition-colors">Terms of Use</a>
                 <a id="refund-policy-link" href="#" class="mx-3 hover:text-green-400 transition-colors">Refund Policy</a>
                 <a id="contact-us-link" href="#" class="mx-3 hover:text-green-400 transition-colors">Contact Us</a>
                 <a href="/sitemap" class="mx-3 hover:text-green-400 transition-colors">Sitemap</a>
            </div>
            <p class="text-sm">&copy; 2025 <strong class="font-semibold text-gray-400">zerofundedhustle</strong>. All Rights Reserved.</p>
            <p class="text-xs mt-2">Built in India, for the Indian Hustler.</p>
        </div>
    </footer>

    <!-- All Modals for Popups -->
    <div id="privacy-modal" class="hidden fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4 modal-overlay opacity-0">
        <div class="modal-container bg-[#0D1117] border border-gray-800 rounded-lg w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 opacity-0">
            <div class="flex-shrink-0 p-6 border-b border-gray-800 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-200">Privacy Policy</h2>
                <button data-close-modal="privacy-modal" class="text-gray-500 hover:text-white text-3xl">&times;</button>
            </div>
            <div class="modal-content flex-grow overflow-y-auto p-6">
                <p class="text-sm text-gray-500 mb-6">Last Updated: October 14, 2025</p>
                <div class="space-y-4 text-gray-400">
                    <p>Welcome to <strong class="font-semibold text-gray-300">zerofundedhustle</strong> (“we,” “our,” “us”). Your privacy matters to us. This Privacy Policy explains how we collect, use, and protect your personal information when you visit <strong class="font-semibold text-gray-300">zerofundedhustle.com</strong> or make a purchase from us.</p>
                    <div><h3 class="font-semibold text-gray-300">1. Information We Collect</h3><p>We collect details like your name and email when you purchase, basic usage data (IP address, browser type), and use cookies to improve site performance.</p></div>
                    <div><h3 class="font-semibold text-gray-300">2. How We Use Your Information</h3><p>We use your data to: Process and deliver ebook purchases, communicate with you, send updates (if you opt in), and improve our website.</p></div>
                    <div><h3 class="font-semibold text-gray-300">3. Payments</h3><p>All payments are processed securely through Razorpay. We don’t store your card details.</p></div>
                    <div><h3 class="font-semibold text-gray-300">4. Data Sharing</h3><p>We do not sell or rent your data. We only share it with essential service providers like payment processors.</p></div>
                    <div><h3 class="font-semibold text-gray-300">5. Contact Us</h3><p>Questions? Contact us at: Email: hello@zerofundedhustle.com</p></div>
                </div>
            </div>
            <div class="flex-shrink-0 p-4 border-t border-gray-800 text-center">
                <a href="/privacypolicy" class="text-sm accent-color hover:underline">For the full details, please click here</a>
            </div>
        </div>
    </div>
    <div id="terms-modal" class="hidden fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4 modal-overlay opacity-0">
         <div class="modal-container bg-[#0D1117] border border-gray-800 rounded-lg w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 opacity-0">
            <div class="flex-shrink-0 p-6 border-b border-gray-800 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-200">Terms of Use</h2>
                <button data-close-modal="terms-modal" class="text-gray-500 hover:text-white text-3xl">&times;</button>
            </div>
            <div class="modal-content flex-grow overflow-y-auto p-6">
                <p class="text-sm text-gray-500 mb-6">Last Updated: October 14, 2025</p>
                <div class="space-y-4 text-gray-400">
                    <p>By accessing or using <strong class="font-semibold text-gray-300">zerofundedhustle.com</strong>, you agree to these Terms of Use. If you don’t agree, please don’t use the site.</p>
                    <div><h3 class="font-semibold text-gray-300">1. Use of Our Website</h3><p>You agree to use this website only for lawful purposes. You must not reproduce, sell, or distribute our content or ebooks without permission.</p></div>
                    <div><h3 class="font-semibold text-gray-300">2. Products and Payments</h3><p>All prices are in INR. Payments are processed securely. As our products are digital, please refer to our Refund Policy for details on eligibility.</p></div>
                    <div><h3 class="font-semibold text-gray-300">3. Intellectual Property</h3><p>All content on this website, including the ebook, belongs to <strong class="font-semibold text-gray-300">zerofundedhustle</strong>. Purchasing an ebook gives you a personal license to read it, not to share or resell it.</p></div>
                    <div><h3 class="font-semibold text-gray-300">4. Disclaimer</h3><p>All content is for educational purposes only. We don’t guarantee specific results. Any actions you take are your responsibility.</p></div>
                     <div><h3 class="font-semibold text-gray-300">5. Governing Law</h3><p>These terms are governed by the laws of India. Any disputes will be handled in the courts of Punjab, India.</p></div>
                    <div><h3 class="font-semibold text-gray-300">6. Contact</h3><p>For any questions about these Terms, reach us at: Email: hello@zerofundedhD hust.com</p></div>
                </div>
            </div>
            <div class="flex-shrink-0 p-4 border-t border-gray-800 text-center">
                <a href="/termsofuse" class="text-sm accent-color hover:underline">For the full details, please click here</a>
            </div>
        </div>
    </div>
     <div id="refund-modal" class="hidden fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4 modal-overlay opacity-0">
        <div class="modal-container bg-[#0D1117] border border-gray-800 rounded-lg w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 opacity-0">
            <div class="flex-shrink-0 p-6 border-b border-gray-800 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-200">Refund Policy</h2>
                <button data-close-modal="refund-modal" class="text-gray-500 hover:text-white text-3xl">&times;</button>
            </div>
            <div class="modal-content flex-grow overflow-y-auto p-6">
                <p class="text-sm text-gray-500 mb-6">Last Updated: October 14, 2025</p>
                <div class="space-y-4 text-gray-400">
                    <p>At <strong class="font-semibold text-gray-300">zerofundedhustle</strong>, our mission is to create guides that actually move people forward. That’s why we offer a 14-Day Action-Based Refund Guarantee.</p>
                    <div><h3 class="font-semibold text-gray-300">1. Our 14-Day Action-Based Guarantee</h3><p>If you’ve read the ebook, actively applied the lessons, and still feel you didn’t gain value, you can request a full refund within 14 days of purchase. We’ll just ask for reasonable proof that you gave it a fair try (like notes or summaries of what you applied). This isn’t to make it hard—it’s to make sure you took action before deciding it didn’t work.</p></div>
                    <div><h3 class="font-semibold text-gray-300">2. How to Request a Refund</h3><p>Email hello@zerofundedhustle.com with your order number and a short explanation of the action you took. Once verified, we’ll process your refund within 5–10 business days.</p></div>
                    <div><h3 class="font-semibold text-gray-300">3. Eligibility</h3><p>Refunds will not be granted for buyers who haven’t read, tried, or applied the content. We designed this policy to reward action-takers, not just readers.</p></div>
                    <div><h3 class="font-semibold text-gray-300">4. Contact</h3><p>If you’ve genuinely done the work and still feel it didn’t help, we’ll honor our word. Reach out at: Email: hello@zerofundedhustle.com</p></div>
                </div>
            </div>
             <div class="flex-shrink-0 p-4 border-t border-gray-800 text-center">
                <a href="/refundpolicy" class="text-sm accent-color hover:underline">For the full details, please click here</a>
            </div>
        </div>
    </div>
    <div id="contact-modal" class="hidden fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4 modal-overlay opacity-0">
        <div class="modal-container bg-[#0D1117] border border-gray-800 rounded-lg w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 opacity-0">
            <div class="flex-shrink-0 p-6 border-b border-gray-800 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-200">Contact Us</h2>
                <button data-close-modal="contact-modal" class="text-gray-500 hover:text-white text-3xl">&times;</button>
            </div>
            <div class="modal-content flex-grow overflow-y-auto p-6">
                 <p class="text-sm text-gray-500 mb-6">Last Updated: October 14, 2025</p>
                <div class="space-y-4 text-gray-400">
                    <p class="text-lg">We’re here to help. If you have any questions about your purchase, need support, or just want to share feedback, reach out — we actually read every message.</p>
                    <div>
                        <h3 class="font-semibold text-gray-300 text-lg">📬 Email</h3>
                        <a href="mailto:hello@zerofundedhustle.com" class="text-green-400 hover:underline">hello@zerofundedhustle.com</a>
                        <p class="text-sm">We typically reply. within 24–48 hours (Mon–Fri).</p>
                    </div>
                     <div>
                        <h3 class="font-semibold text-gray-300 text-lg mt-6">🧭 Common Reasons to Contact Us</h3>
                        <ul class="list-disc list-inside mt-2">
                            <li>Questions about ebooks or future releases</li>
                            <li>Issues with payments or downloads</li>
                            <li>Refund or guarantee requests</li>
                            <li>Collaboration or partnership inquiries</li>
                            <li>General feedback or support</li>
                        </ul>
                    </div>
                    <div class="mt-6 bg-gray-900 border border-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-300">💡 Tip</h3>
                        <p>If you’re contacting us about an order, include your order number or the email used at checkout — it helps us resolve things faster.</p>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 p-4 border-t border-gray-800 text-center">
                <a href="/contactus" class="text-sm accent-color hover:underline">For the full details, please click here</a>
            </div>
        </div>
    </div>

    <!-- Full JavaScript for Popups -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modals = {
                'privacy-modal': document.getElementById('privacy-modal'),
                'terms-modal': document.getElementById('terms-modal'),
                'refund-modal': document.getElementById('refund-modal'),
                'contact-modal': document.getElementById('contact-modal')
            };

            const openModal = (modalId) => {
                const modal = modals[modalId];
                if (!modal) return;
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    if(modal.querySelector('.modal-container')) {
                        modal.querySelector('.modal-container').classList.remove('scale-95', 'opacity-0');
                    }
                }, 10);
            };

            const closeModal = (modalId) => {
                const modal = modals[modalId];
                if (!modal) return;
                if(modal.querySelector('.modal-container')) {
                    modal.querySelector('.modal-container').classList.add('scale-95', 'opacity-0');
                }
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };

            document.getElementById('privacy-policy-link')?.addEventListener('click', (e) => {
                e.preventDefault();
                openModal('privacy-modal');
            });

            document.getElementById('terms-of-use-link')?.addEventListener('click', (e) => {
                e.preventDefault();
                openModal('terms-modal');
            });

            document.getElementById('refund-policy-link')?.addEventListener('click', (e) => {
                e.preventDefault();
                openModal('refund-modal');
            });

            document.getElementById('contact-us-link')?.addEventListener('click', (e) => {
                e.preventDefault();
                openModal('contact-modal');
            });
            
            document.querySelectorAll('[data-close-modal]').forEach(button => {
                button.addEventListener('click', () => {
                    closeModal(button.dataset.closeModal);
                });
            });

            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        closeModal(overlay.id);
                    }
                });
            });
        });
    </script>
</body>
</html>

