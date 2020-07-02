<?php

return [
    'mixins' => ['filepicker', 'upload'],
    'props' => [
        'allowed' => function (array $allowed = null) {
            return $allowed;
        },
        'autofocus' => function (bool $autofocus = false) {
            return $autofocus;
        },
        'default' => function ($default = null) {
            return $default;
        },
        'disallowed' => function (array $disallowed = null) {
            return $disallowed;
        },
        /**
         * Sets the options for the files picker
         */
        'files' => function ($files = []) {
            if (is_string($files) === true) {
                return ['query' => $files];
            }

            if (is_array($files) === false) {
                $files = [];
            }

            return $files;
        },
        'pretty' => function (bool $pretty = true) {
            return $pretty;
        },
        'spellcheck' => function (bool $spellcheck = true) {
            return $spellcheck;
        },
        'value' => function ($value = null) {
            return $this->toValue($value);
        },
    ],
    'computed' => [
        'isSupported' => function () {
            if (version_compare($this->kirby()->version(), '3.3.0', '<')) {
                throw new Exception('The editor requires Kirby version 3.3.0 or higher');
            }
        },
        'default' => function () {
            return $this->toValue($this->default);
        }
    ],
    'methods' => [
        'toBlocks' => function ($value) {
            return Kirby\Editor\Blocks::factory($value, $this->model());
        },
        'toValue' => function ($value) {
            if ($this->inline) {
                if (is_array($value) === false) {
                    try {
                        $value = Json::decode((string)$value);
                    } catch (Throwable $e) {
                        return $value;
                    }
                }

                return $this->toBlocks($value)->toHtml();
            } else {
                return $this->toBlocks($value)->toArray();
            }
        }
    ],
    'save' => function ($value) {
        if (empty($value)) {
            return '';
        }

        if ($this->inline) {
            return $value;
        }

        $value = $this
            ->toBlocks($value)
            ->toStorage();

        if ($this->pretty === true) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return json_encode($value);
    },
    'api' => function () {
        return [
            [
                'pattern' => 'files',
                'action' => function () {
                    $field = $this->field();
                    $files = $field->files();

                    // inject stuff from the query
                    $files['page']   = $this->requestQuery('page');
                    $files['search'] = $this->requestQuery('search');

                    return $field->filepicker($files);
                }
            ],
            [
                'pattern' => 'upload',
                'method'  => 'POST',
                'action'  => function () {
                    return $this->field()->upload($this, $this->field()->uploads(), function ($file) {
                        return [
                            'filename' => $file->filename(),
                            'link'     => $file->panelUrl(true),
                            'url'      => $file->url(),
                        ];
                    });
                }
            ],
            [
                'pattern' => 'paste',
                'method' => 'POST',
                'action' => function () {
                    return Kirby\Editor\Blocks::factory(get('html'), $this->field()->model())->toArray();
                }
            ],
            [
                'pattern' => 'import',
                'method' => 'POST',
                'action' => function () {
                    $api   = $this;
                    $field = $this->field();

                    return $this->upload(function ($source, $filename) use ($api, $field) {
                        $html   = F::read($source);
                        $blocks = Kirby\Editor\Blocks::factory($html, $field->model())->toArray();

                        return $blocks;
                    });
                }
            ],
            [
                'pattern' => 'export',
                'method' => 'POST',
                'action' => function () {
                    $data = get('data');
                    $type = get('type');

                    $blocks = Kirby\Editor\Blocks::factory($data, $this->field()->model());

                    switch ($type) {
                        case 'md':
                            $result = $blocks->toMarkdown();
                            break;
                        case 'html':
                            $result = $blocks->toHtml();
                            break;
                        default:
                            $result = json_encode($blocks->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            break;
                    }

                    return [
                        'data' => $result
                    ];
                }
            ],
        ];
    },
];
