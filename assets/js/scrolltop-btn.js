	(function(){	
		/* / / / / / / / / / / / / */
		/* / / HELPER CLASSES / / */
		/* / / / / / / / / / / / / */
		// DEBOUNCE
		const debounce = function(func, delay){
			let timer;
			return function () {     //anonymous function
			const context = this; 
			const args = arguments;
			clearTimeout(timer); 
			timer = setTimeout(()=> {
				  func.apply(context, args)
				},delay);
			}
		}
		// THROTTLE
		const throttle = (func, limit) => {
			let lastFunc;
			let lastRan;
			return function() {
				const context = this;
				const args = arguments;
				if (!lastRan) {
				  func.apply(context, args)
				  lastRan = Date.now();
				} else {
				  clearTimeout(lastFunc);
				  lastFunc = setTimeout(function() {
				      if ((Date.now() - lastRan) >= limit) {
				        func.apply(context, args);
				        lastRan = Date.now();
				      }
				   }, limit - (Date.now() - lastRan));
				}
			}
		}

		const scrollToTop = () => {
				const c = document.documentElement.scrollTop || document.body.scrollTop

			if (c > 0) {
				window.requestAnimationFrame(scrollToTop)
				window.scrollTo(0, c - c / 8)
			}
		}

		let $sT,
		_scroll2TopBTN,
		_scrollRatio = 1.75;

		function toggleScrollTop(showBtn) {
			if(!showBtn && _scroll2TopBTN.classList.contains('show')) {
			_scroll2TopBTN.classList.replace('show', 'hide');
			} else if(showBtn && _scroll2TopBTN.classList.contains('hide')) {
			_scroll2TopBTN.classList.replace('hide', 'show');
			}
		}

		function addWindowListener() {
			window.addEventListener('scroll', 
			throttle(function () {
			$sT = window.pageYOffset;				
			if($sT > window.innerHeight * 0.5) {
				toggleScrollTop(true);
				return;
			}
			toggleScrollTop(false);
			}, 500)
			);
		}
		function addScrollListener(element) {
			element.addEventListener('click', () => {
			scrollToTop()
			}, false);
		}
		function initScroll2TopBtn() {
			// DECIDE WHETHER CONTENT IS LONG ENOUGH 
			// FOR SCROLLTOP BTN TO MAKE SENSE
			const body = document.body;
			const html = document.documentElement;
			const _maxH = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
			if(_maxH / window.innerHeight < _scrollRatio) return;

			// IF SO, CREATE SCROLL2TOP BTN
			_scroll2TopBTN = document.createElement('button');
			_scroll2TopBTN.setAttribute("class", "scroll-top hide");
			_scroll2TopBTN.setAttribute("id", "scroll_top_ID");
			_scroll2TopBTN.setAttribute("value", "Scroll to top");

			document.body.appendChild(_scroll2TopBTN);

			addScrollListener(_scroll2TopBTN);
			addWindowListener();
		}

		initScroll2TopBtn();
})();