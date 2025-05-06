describe('User Login', () => {
    it('should allow a user to log in with valid credentials', () => {
        cy.login('user@example.tld', 'password');

        cy.url().should('include', '/');
        cy.contains('User Dashboard').should('be.visible');
    });
});

describe('User Login with invalid credentials', () => {
    it('login should not succeeds with invalid credentials', () => {
        cy.login('not-exists@example.tld', 'password');

        cy.url().should('include', '/login');
        cy.contains('Login failed. Please try again.').should('be.visible');
    });
});
