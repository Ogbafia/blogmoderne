describe('Authentication & Admin Access', () => {
  it('Should display the home page with an articles section', () => {
    cy.visit('/')
    cy.contains('h1', 'Blog Moderne').should('exist')
  })

  it('Should redirect guest from admin to login', () => {
    cy.visit('/admin')
    cy.url().should('include', '/auth/login')
  })

  it('Should login and access admin dashboard', () => {
    cy.visit('/auth/login')
    cy.get('input[type="email"]').type('admin@example.com')
    cy.get('input[type="password"]').type('password')
    cy.get('button[type="submit"]').click()

    // Assuming it redirects to /admin or we can navigate there
    cy.visit('/admin')
    cy.url().should('include', '/admin')
    cy.contains('Aperçu Global').should('exist')
  })
})
