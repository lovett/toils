/*eslint-env node */
module.exports = {
    "extends": "standard",
    "plugins": [
        "standard",
        "promise"
    ],
    "rules": {
        "indent": ["error", 4],
        "semi": ["error", "always"],
        "no-new": "off",
        "no-unused-vars": [2, {"vars": "local", "args": "after-used"}]
    }
};