describe('Edit account', () => {
    it('edit user account of the current logged in user should succeed', () => {
        cy.login('user@example.tld', 'password');

        cy.get('.navbar .dropdown-toggle').click();
        cy.get('a[id="my-account-link"]').click();

        cy.get('h2').contains('My account').should('be.visible');
        cy.get('#account-name').should('contain.text', 'Name:');
        cy.get('#account-email').should('contain.text', 'Email address:');
        cy.get('#account-teams').contains('Teams: 2').should('be.visible');

        cy.get('#edit-button').click();

        cy.get('.modal-title').contains('Edit account data').should('be.visible');

        cy.get('input.given-name').clear();
        cy.get('input.given-name').type('Hans');
        cy.get('input.family-name').clear();
        cy.get('input.family-name').type('Mayer');

        cy.intercept('PATCH', '/me/1').as('accountUpdate');

        cy.get('.modal #submit-btn').click();

        cy.wait('@accountUpdate');

        cy.get('#account-name').contains('Name: Hans Mayer').should('be.visible');
        cy.get('#account-teams').contains('Teams: 2').should('be.visible');
    });
});
