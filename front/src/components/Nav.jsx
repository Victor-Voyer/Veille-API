import React from 'react'
import { Link, useLocation } from 'react-router-dom'

const Nav = () => {
    const location = useLocation()
    
    const isActive = (path) => {
        if (path === '/' && location.pathname === '/') return true
        if (path !== '/' && location.pathname.startsWith(path)) return true
        return false
    }
    
    return (
        <nav className="main-nav">
            <div className="container">
                <div className="nav-content">
                    <Link to="/" className="nav-logo">
                        📚 Veille Tech
                    </Link>
                    <div className="nav-links">
                        <Link 
                            to="/" 
                            className={`nav-link ${isActive('/') ? 'active' : ''}`}
                        >
                            Accueil
                        </Link>
                        <Link 
                            to="/facts/new" 
                            className={`nav-link ${isActive('/facts/new') ? 'active' : ''}`}
                        >
                            Créer un fact
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
    )
}

export default Nav
