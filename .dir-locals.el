;;; Directory Local Variables
;;; For more information see (info "(emacs) Directory Variables")

((php-mode
  (eval . (progn
            (set (make-local-variable 'project-root)
                 (file-name-directory
                  (let ((d (dir-locals-find-file ".")))
                    (if (stringp d) d (car d)))))
            (customize-set-variable 'flycheck-php-phpcs-executable (expand-file-name "vendor/bin/phpcs" project-root))
            (customize-set-variable 'flycheck-phpcs-standard (expand-file-name "phpcs.xml" project-root))
            (customize-set-variable 'flycheck-php-phpmd-executable (expand-file-name "vendor/bin/phpmd" project-root))
            (customize-set-variable 'flycheck-phpmd-rulesets (expand-file-name "phpmd.xml" project-root))))))

((js-mode
  (eval . (progn
            (set (make-local-variable 'project-root)
                 (file-name-directory
                  (let ((d (dir-locals-find-file ".")))
                    (if (stringp d) d (car d)))))
            (customize-set-variable 'flycheck-javascript-eslint-executable (expand-file-name "node_modules/.bin/eslint" project-root))))))
