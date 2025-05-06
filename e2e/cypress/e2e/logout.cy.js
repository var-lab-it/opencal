describe('User Logout', () => {
    it('should logs out the user', () => {
        cy.login('user@example.tld', 'password');

        cy.url().should('include', '/');
        cy.contains('User Dashboard').should('be.visible');

        cy.get('.navbar .dropdown-toggle').click();
        cy.get('a[id="logout-link"]').click();

        cy.url().should('include', '/login');
        cy.contains('Login').should('be.visible');
    });
});
