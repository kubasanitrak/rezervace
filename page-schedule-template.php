<?php
/**
 * Template Name: PAGE SCHEDULE
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear.
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage rezervace_theme
 * @since 1.0
 * * @version 1.0
 */

     get_header(); 


?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

     <div class="section section-mar-T full-bleed">
		
	
			<h1 class="page-title <?php echo $CLS; ?>"><?php echo the_title(); ?></h1>
	
			<?php the_content(); ?>
			<!-- template-schedule.php or any page template -->
                <div class="barre-schedule-container">
                    <div class="calendar-header">
                        <button class="nav-btn prev-week">← Previous Week</button>
                        <h2 class="current-week-range"></h2>
                        <button class="nav-btn next-week">Next Week →</button>
                    </div>

                    <div class="calendar-view week-view">
                        <!-- Will be filled by JS -->
                    </div>

                    <div class="legend">
                        <div class="status available">Available</div>
                        <div class="status almost-full">Almost Full</div>
                        <div class="status full">Full</div>
                    </div>
                </div>

                <!-- Modal for selecting number of persons -->
                <div id="addToBasketModal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="modal-close" id="closeModal">×</span>
                        
                        <h2 id="modalLessonName">Add to Basket</h2>
                        <p id="modalLessonInfo"></p>
                        
                        <div class="form-group">
                            <label for="modalPersons">Number of persons:</label>
                            <select id="modalPersons" name="persons">
                                <option value="1">1 person</option>
                                <option value="2">2 persons</option>
                                <option value="3">3 persons</option>
                            </select>
                        </div>
                        
                        <div class="price-preview">
                            <strong>Total: <span id="modalTotalPrice">0 Kč</span></strong>
                            <small>(available spots: <span id="modalAvailable">—</span>)</small>
                        </div>
                        
                        <div class="modal-actions">
                            <button id="cancelAdd" class="btn-secondary">Cancel</button>
                            <button id="confirmAdd" class="btn-primary">Add to Basket</button>
                        </div>
                    </div>
                </div>
                <!-- Modal for selecting number of persons -->
                <!-- <div id="addToBasketModal" class="modal" style="display:none; opacity:0;">
                    <div class="modal-content">
                        <span class="modal-close" id="closeModal">×</span>
                        
                        <h2 id="modalLessonName">Add to Basket</h2>
                        <p id="modalLessonInfo"></p>
                        
                        <div class="form-group">
                            <label>Number of persons:</label>
                            <div class="quantity-stepper">
                                <button id="decrementPersons" class="stepper-btn">-</button>
                                <span id="personsDisplay">1</span>
                                <button id="incrementPersons" class="stepper-btn">+</button>
                            </div>
                        </div>
                        
                        <div class="price-preview">
                            <strong>Total: <span id="modalTotalPrice">0 Kč</span></strong>
                            <small>(available spots: <span id="modalAvailable">—</span>)</small>
                        </div>
                        
                        <div class="modal-actions">
                            <button id="cancelAdd" class="btn-secondary">Cancel</button>
                            <button id="confirmAdd" class="btn-primary">Add to Basket</button>
                        </div>
                    </div>
                </div> -->

                <!-- Success modal (added to basket) -->
<div id="successModal" class="modal" style="display:none;">
    <div class="modal-content success-modal">
        <span class="modal-close" id="closeSuccess">×</span>
        <div class="modal-icon">✓</div>
        <h2>Successfully Added!</h2>
        <p id="successMessage">Your lesson has been added to the basket.</p>
        <div class="modal-actions">
            <button id="continueShopping" class="btn-secondary">Continue Shopping</button>
            <button id="viewBasketBtn" class="btn-primary">View Basket</button>
        </div>
    </div>
</div>

<!-- Error modal (e.g. already in basket, not enough spots) -->
<div id="errorModal" class="modal" style="display:none;">
    <div class="modal-content error-modal">
        <span class="modal-close" id="closeError">×</span>
        <div class="modal-icon error-icon">⚠</div>
        <h2>Oops!</h2>
        <p id="errorMessage">Something went wrong.</p>
        <div class="modal-actions">
            <button id="closeErrorBtn" class="btn-primary">OK</button>
        </div>
    </div>
</div>

<!-- Clear basket confirmation -->
<div id="clearConfirmModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="modal-close" id="closeClear">×</span>
        <h2>Clear Basket?</h2>
        <p>This will remove all items from your basket. This action cannot be undone.</p>
        <div class="modal-actions">
            <button id="cancelClear" class="btn-secondary">Cancel</button>
            <button id="confirmClear" class="btn-danger">Yes, Clear Basket</button>
        </div>
    </div>
</div>
            
            <!-- Add this somewhere visible – e.g. fixed bottom right corner -->
                <div class="basket-float" id="basketFloat">
                    <div class="basket-header">
                        <span>Your Basket (<span id="basketCount">0</span>)</span>
                        <button class="basket-toggle" id="toggleBasket">▼</button>
                    </div>
                    
                    <div class="basket-content" id="basketContent" style="display:none;">
                        <div id="basketItems"></div>
                        <div class="basket-total">
                            Total: <strong id="basketTotal">0 Kč</strong>
                        </div>
                        <button class="btn-checkout" id="goToCheckout" disabled>Proceed to Checkout</button>
                        <button class="btn-clear" id="clearBasket">Clear</button>
                    </div>
                </div>

		</div><!-- END SECTION -->
		
	<?php endwhile; endif; ?>


	<?php get_footer(); ?>


