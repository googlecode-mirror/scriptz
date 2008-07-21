;; Emacs config file slackware
;; $Id$
;; zhuzhu@perlchina.org
;; emacs23 version
;;

;; ======= Add load path =========
(if (fboundp 'normal-top-level-add-subdirs-to-load-path)
(let* ((my-lisp-dir "/home/fred/local/share/emacs/site-lisp")
(default-directory my-lisp-dir))
(setq load-path (cons my-lisp-dir load-path))
(normal-top-level-add-subdirs-to-load-path)))

;; ======= Add Js2 Mode =======
;; http://code.google.com/p/js2-mode/
;; current version is js2-20080521.el
(autoload 'js2-mode "js2" nil t)
(add-to-list 'auto-mode-alist '("\\.js$" . js2-mode))

;; ======= Add PHP Mode =======
(autoload 'php-mode "php-mode" nil t)
(add-to-list 'auto-mode-alist '("\\.php$" . php-mode))

;; disable startup message
(setq inhibit-startup-message t)

;; ======= Set font size and font family to emacs, I love it ;) ======
;; you can use XFT font now!
;(set-face-font 'default "Lucida Console-10")
(global-font-lock-mode t)
(setq font-lock-maximum-decoration t)

;; ======= Highlight current line =========
;;(global-hl-line-mode 1)

;; ======= Set standard indent size ========
(setq standard-indent 2)

;; ======= Line by line scrolling ========
(setq scroll-step 1)

;; ======= Turn off tab characer ======
(setq-default indent-tabs-mode nil)

;; ======= Enable wheel-mouse scrolling =======
(mouse-wheel-mode t)

;; ======= Prevent Backup file creation =======
;(setq make-backup-files nil)

;; ======= Saving Backup file to a specific directory =======
;; Enable backup files.
(setq make-backup-files nil)
;; enable versioning with defualt valuess 
(setq version-control nil)
;; Save all backup file in this directory.
(setq backup-directory-alist (quote ((".*" . "~/.emacs-backups/"))))

;; ======= Enable line and column number ======
(line-number-mode 1)
(column-number-mode 1)

;; ======= Set the fill column =======
(setq-default fill-column 72)

;; ======= ENable auto fill mode =======
(setq auto-fill-mode 1)

;; ======= Treat new buffers as text
(setq default-marjor-mode 'text-mode)

;; alias y to yes and n to no
(defalias 'yes-or-no-p 'y-or-n-p)

;; highlight matches from searches
(setq isearch-highlight t)
(setq search-highlight t)
(setq-default transient-mark-mode t)

(when (fboundp 'blink-cursor-mode)
  (blink-cursor-mode -1))

;; turn off the toolbar
;; (if (>= emacs-major-version 21)
;;    (tool-bar-mode -1))

;;; 
(show-paren-mode t)
(setq make-backup-files nil)
;(setq visible-bell t)
;(tool-bar-mode nil)

(custom-set-variables
  ;; custom-set-variables was added by Custom.
  ;; If you edit it by hand, you could mess it up, so be careful.
  ;; Your init file should contain only one such instance.
  ;; If there is more than one, they won't work right.
 '(blink-cursor-mode nil)
 '(column-number-mode t))
(custom-set-faces
  ;; custom-set-faces was added by Custom.
  ;; If you edit it by hand, you could mess it up, so be careful.
  ;; Your init file should contain only one such instance.
  ;; If there is more than one, they won't work right.
 )

;; ======= Load color-theme lisp ========
;; Must at end of the config file!
(require 'color-theme)
(color-theme-initialize)
;(color-theme-select)
(color-theme-robin-hood)
