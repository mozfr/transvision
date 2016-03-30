module.exports = {
    "env": {
        "browser": true,
        "jquery": true
    },
    "extends": "eslint:recommended",
    "globals": {
        "Clipboard": true,
        "supportedLocales": true
    },
    "rules": {
        "camelcase": [
            2,
            {
                "properties": "always"
            }
        ],
        "curly": [
            2,
            "all"
        ],
        "eqeqeq": [
            2,
            "smart"
        ],
        "indent": [
            2,
            4
        ],
        "linebreak-style": [
            2,
            "unix"
        ],
        "new-cap": 2,
        "no-spaced-func": 2,
        "one-var-declaration-per-line": [
            2,
            "always"
        ],
        "quotes": [
            2,
            "single"
        ],
        "semi": [
            2,
            "always"
        ],
        "space-before-function-paren": [
            2,
            "never"
        ]
    }
};
