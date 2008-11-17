;; Emacs config file slackware
;; $Id$
;; zhuzhu@cpan.org
;; emacs23 version
;; Refrence: http://docs.huihoo.com/homepage/shredderyin/emacs_elisp.html
;; 

;; ======= Add load path =========
(if (fboundp 'normal-top-level-add-subdirs-to-load-path)
(let* ((my-lisp-dir "/home/fred/local/share/emacs/site-lisp")
(default-directory my-lisp-dir))
(setq load-path (cons my-lisp-dir load-path))
(normal-top-level-add-subdirs-to-load-path)))

;; ======= Require Hack File ======
(require 'htmlize-hack)

;; ======= Fonts =======
(set-face-font 'menu "Lucida Console-10")
(set-frame-font "Monaco-10")
(set-fontset-font (frame-parameter nil 'font)
   'han '("Wenquanyi Zen Hei" . "unicode-bmp"))

;; ======= Bar-cursor-mode ======
;(setq-default cursor-type 'bar)
(setq frame-title-format ' buffer-file-name )
(transient-mark-mode t)
(setq enable-recursive-minibuffers t)
(setq default-fill-column 80)
(setq scroll-margin 1 scroll-conservatively 5000)


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

;; ======= Change Tab Width ========
(setq tab-width 4
      indent-tabs-mode t
      c-basic-offset 4)
;; ======= Highlight current line =========
;;(global-hl-line-mode 1)

;; ======= Set standard indent size ========
(setq standard-indent 4)

;; ======= Line by line scrolling ========
(setq scroll-step 0)

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

;; ======= Character =======
(setq-default line-spacing 1)

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
 '(fixed-pitch ((t nil)))
 '(variable-pitch ((t nil))))

;; ======= yasnippet =======
(require 'yasnippet)
(yas/initialize)
(yas/load-directory "/home/fred/local/share/emacs/site-lisp/yasnippet/snippets/")

;; ======= Auto Compulete ========
;; enable skeleton-pair insert globally
(setq skeleton-pair t)
; (setq skeleton-pair-on-word t) ; apply skeleton trick even in front of a word.
(global-set-key "("  'skeleton-pair-insert-maybe)
(global-set-key "["  'skeleton-pair-insert-maybe)
(global-set-key "{"  'skeleton-pair-insert-maybe)
(global-set-key "\"" 'skeleton-pair-insert-maybe)
(global-set-key "'"  'skeleton-pair-insert-maybe)

;; ======= Useful Config =========
;; Visit here get more
;; http://docs.huihoo.com/homepage/shredderyin/emacs_elisp.html
(require 'ibuffer)
(global-set-key (kbd "C-x C-b") 'ibuffer)
(require 'ido)
(ido-mode t)
;; 
(global-set-key "%" 'match-paren)
          
(defun match-paren (arg)
  "Go to the matching paren if on a paren; otherwise insert %."
  (interactive "p")
  (cond ((looking-at "\\s\(") (forward-list 1) (backward-char 1))
	((looking-at "\\s\)") (forward-char 1) (backward-list 1))
	(t (self-insert-command (or arg 1)))))

;; ======= Another Setting =======
(setq kill-ring-max 200)
(setq default-fill-column 60)
(setq scroll-margin 3
      scroll-conservatively 5000)

;; ======= W3m ========
(require 'w3m-load)
(setq w3m-command-arguments '("-cookie" "-F"))
(setq w3m-use-cookies t)
(setq w3m-home-page "http://home.lz3.org/zhuzhu")
(setq w3m-tab-width 8)
(setq browse-url-browser-function 'w3m-browse-url)
(autoload 'w3m-browse-url "w3m" "Ask a WWW browser to show a URL." t)
(global-set-key (kbd "C-c o") 'browse-url-at-point)

;; ======= Set User Infomation =======
(setq user-full-name "Fred Chu")
(setq user-mail-address "fred1982@gmail.com")

;; ======= Emacs Wiki ========
(require 'emacs-wiki)
;; ======= Planner ========
(require 'planner)

;; ======= Load color-theme lisp ========
;; Must at end of the config file!
(require 'color-theme)
(color-theme-initialize)
;(color-theme-select)
(color-theme-robin-hood)
