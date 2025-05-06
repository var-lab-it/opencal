const {defineConfig} = require("cypress");

module.exports = defineConfig({
    e2e: {
        baseUrl: process.env.CYPRESS_BASE_URL || 'http://localhost',
        setupNodeEvents(on, config) {
            // implement node event listeners here
        },
    },
});
