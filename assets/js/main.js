/**
 * Advisers Multipurpose — main.js
 *
 * Production-grade vanilla JavaScript.
 * No dependencies.
 *
 * Responsibilities:
 * - Responsive primary navigation
 * - Mobile submenu handling
 * - Accessible sidebar collapse/expand
 *
 * Important:
 * This script intentionally does NOT set a max-height on the
 * main content area. WordPress pages should use normal document
 * scrolling rather than an internally scrolling content column.
 */

( function () {
	'use strict';

	/**
	 * Run after the DOM is ready.
	 */
	function init() {
		initMobileNavigation();
		initMobileSubmenus();
		initSidebarToggle();
	}

	/**
	 * Primary mobile navigation.
	 */
	function initMobileNavigation() {
		var toggle = document.querySelector( '.menu-toggle' );
		var nav = document.getElementById( 'site-navigation' );

		if ( ! toggle || ! nav ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var isOpen = nav.classList.toggle( 'is-open' );

			toggle.setAttribute(
				'aria-expanded',
				isOpen ? 'true' : 'false'
			);
		} );

		/*
		 * If the viewport changes from mobile to desktop while the
		 * mobile menu is open, reset the mobile state.
		 */
		var mediaQuery = window.matchMedia( '(max-width: 782px)' );

		var handleViewportChange = function ( event ) {
			if ( ! event.matches ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );

				closeAllSubmenus();
			}
		};

		if ( typeof mediaQuery.addEventListener === 'function' ) {
			mediaQuery.addEventListener(
				'change',
				handleViewportChange
			);
		} else if ( typeof mediaQuery.addListener === 'function' ) {
			/*
			 * Older browser fallback.
			 */
			mediaQuery.addListener( handleViewportChange );
		}
	}


	/**
	 * Mobile submenu handling.
	 *
	 * The existing implementation prevented every parent-menu link
	 * from navigating on mobile. This version only intercepts the
	 * first activation when a submenu is currently closed.
	 *
	 * Once the submenu is open, the link can navigate normally.
	 */
	function initMobileSubmenus() {
		var parentLinks = document.querySelectorAll(
			'.main-navigation li.menu-item-has-children > a'
		);

		if ( ! parentLinks.length ) {
			return;
		}

		parentLinks.forEach( function ( link ) {
			link.addEventListener( 'click', function ( event ) {
				if ( window.innerWidth > 782 ) {
					return;
				}

				var parentItem = link.parentElement;

				if ( ! parentItem ) {
					return;
				}

				var submenu = parentItem.querySelector(
					':scope > ul'
				);

				if ( ! submenu ) {
					return;
				}

				var isOpen = parentItem.classList.contains(
					'is-open'
				);

				/*
				 * First click opens the submenu instead of navigating.
				 */
				if ( ! isOpen ) {
					event.preventDefault();

					closeSiblingSubmenus( parentItem );

					parentItem.classList.add( 'is-open' );

					link.setAttribute(
						'aria-expanded',
						'true'
					);
				}
			} );
		} );
	}


	/**
	 * Close sibling submenus.
	 *
	 * @param {HTMLElement} currentItem Current menu item.
	 */
	function closeSiblingSubmenus( currentItem ) {
		var parent = currentItem.parentElement;

		if ( ! parent ) {
			return;
		}

		var siblings = parent.querySelectorAll(
			':scope > li.menu-item-has-children.is-open'
		);

		siblings.forEach( function ( sibling ) {
			if ( sibling === currentItem ) {
				return;
			}

			sibling.classList.remove( 'is-open' );

			var siblingLink = sibling.querySelector(
				':scope > a'
			);

			if ( siblingLink ) {
				siblingLink.setAttribute(
					'aria-expanded',
					'false'
				);
			}
		} );
	}


	/**
	 * Close all mobile submenus.
	 */
	function closeAllSubmenus() {
		var openItems = document.querySelectorAll(
			'.main-navigation li.menu-item-has-children.is-open'
		);

		openItems.forEach( function ( item ) {
			item.classList.remove( 'is-open' );

			var link = item.querySelector( ':scope > a' );

			if ( link ) {
				link.setAttribute(
					'aria-expanded',
					'false'
				);
			}
		} );
	}


	/**
	 * Sidebar collapse/expand.
	 *
	 * The state is persisted locally on the visitor's device.
	 *
	 * IMPORTANT:
	 * This function intentionally does not manipulate #primary's
	 * height or scrolling behavior.
	 */
	function initSidebarToggle() {
		var sidebarToggle = document.querySelector(
			'.sidebar-toggle'
		);

		var sidebar = document.getElementById( 'secondary' );

		var storageKey = 'am-sidebar-collapsed';

		if ( ! sidebarToggle || ! sidebar ) {
			return;
		}


		/**
		 * Apply sidebar state.
		 *
		 * @param {boolean} collapsed Whether the sidebar is collapsed.
		 */
		function applySidebarState( collapsed ) {
			sidebar.classList.toggle(
				'sidebar-collapsed',
				collapsed
			);

			sidebarToggle.setAttribute(
				'aria-expanded',
				collapsed ? 'false' : 'true'
			);
		}


		/**
		 * Read persisted sidebar state safely.
		 */
		function getStoredState() {
			try {
				return (
					window.localStorage &&
					window.localStorage.getItem( storageKey ) === 'true'
				);
			} catch ( error ) {
				return false;
			}
		}


		/**
		 * Save sidebar state safely.
		 *
		 * @param {boolean} collapsed Whether the sidebar is collapsed.
		 */
		function saveStoredState( collapsed ) {
			try {
				if ( window.localStorage ) {
					window.localStorage.setItem(
						storageKey,
						String( collapsed )
					);
				}
			} catch ( error ) {
				/*
				 * Storage may be disabled by the browser,
				 * privacy settings or private browsing.
				 */
			}
		}


		/*
		 * Restore previous visitor preference.
		 */
		applySidebarState( getStoredState() );


		/*
		 * Toggle sidebar.
		 */
		sidebarToggle.addEventListener( 'click', function () {
			var collapsed = ! sidebar.classList.contains(
				'sidebar-collapsed'
			);

			applySidebarState( collapsed );
			saveStoredState( collapsed );
		} );
	}


	/*
	 * DOM ready.
	 */
	if ( document.readyState === 'loading' ) {
		document.addEventListener(
			'DOMContentLoaded',
			init,
			{ once: true }
		);
	} else {
		init();
	}

}() );