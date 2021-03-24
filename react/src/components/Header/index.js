import React, { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import logo from '../../images/logo-dark.png';
import { AppBar, Badge, Button, fade, IconButton, InputBase, makeStyles, Menu, MenuItem, Toolbar, Typography } from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu';
import SearchIcon from '@material-ui/icons/Search';
import AccountCircle from '@material-ui/icons/AccountCircle';
import NotificationsIcon from '@material-ui/icons/Notifications';
import MoreIcon from '@material-ui/icons/MoreVert';
import LocalMallOutlinedIcon from '@material-ui/icons/LocalMallOutlined';
import SettingsIcon from '@material-ui/icons/Settings';

const useStyles = makeStyles((theme) => ({
    grow: {
        flexGrow: 1,
    },
    menuButton: {
        marginRight: theme.spacing(2),
    },
    title: {
        display: 'none',
        [theme.breakpoints.up('sm')]: {
        display: 'block',
        },
    },
    sectionDesktop: {
        display: 'none',
        [theme.breakpoints.up('md')]: {
        display: 'flex',
        },
    },
    sectionMobile: {
        display: 'flex',
        [theme.breakpoints.up('md')]: {
        display: 'none',
        },
    },
}));

const Header = () => {
    const classes = useStyles();
    const [anchorEl, setAnchorEl] = useState(null);
    const [anchorEl1, setAnchorEl1] = useState(null);
    const [mobileMoreAnchorEl, setMobileMoreAnchorEl] = useState(null);

    const isMenuOpen = Boolean(anchorEl);
    const isMobileMenuOpen = Boolean(mobileMoreAnchorEl);

    const handleProfileMenuOpen = (event) => {
        setAnchorEl(event.currentTarget);
    };
    const handleSettingMenuOpen = (event) => {
        setAnchorEl1(event.currentTarget);
    };

    const handleMobileMenuClose = () => {
        setMobileMoreAnchorEl(null);
    };

    const handleMenuClose = () => {
        setAnchorEl(null);
        handleMobileMenuClose();
    };

    const handleMobileMenuOpen = (event) => {
        setMobileMoreAnchorEl(event.currentTarget);
    };

    const menuId = 'primary-search-account-menu';
    const renderMenu = (
        <Menu
            anchorEl={anchorEl}
            anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
            id={menuId}
            keepMounted
            transformOrigin={{ vertical: 'top', horizontal: 'right' }}
            open={isMenuOpen}
            onClose={handleMenuClose}
            >
            <MenuItem><Link to="/signin" className="" color="inherit">Sign In</Link></MenuItem>
            <MenuItem><Link to="/signup" className="" color="inherit">Sign Up</Link></MenuItem>
        </Menu> 
    );

    const settingMenuId = 'primary-setting-menu';
    const renderSettingMenu = (
        <Menu
            anchorEl={anchorEl1}
            anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
            id={settingMenuId}
            keepMounted
            transformOrigin={{ vertical: 'top', horizontal: 'right' }}
            open={isMenuOpen}
            onClose={handleMenuClose}
            >
            <MenuItem><Link to="/add-product" className="" color="inherit">Add Product</Link></MenuItem>
            <MenuItem><Link to="/add-category" className="" color="inherit">Add Category</Link></MenuItem>
        </Menu> 
    );

    const mobileMenuId = 'primary-search-account-menu-mobile';
    const renderMobileMenu = (
        <Menu
            anchorEl={mobileMoreAnchorEl}
            anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
            id={mobileMenuId}
            keepMounted
            transformOrigin={{ vertical: 'top', horizontal: 'right' }}
            open={isMobileMenuOpen}
            onClose={handleMobileMenuClose}
        >
        <MenuItem>
            <IconButton aria-label="show 11 new notifications" color="inherit">
            <Badge badgeContent={11} color="secondary">
                <NotificationsIcon />
            </Badge>
            </IconButton>
            <p>Notifications</p>
        </MenuItem>
        <MenuItem>
            <IconButton aria-label="show 11 new notifications" color="inherit">
            <Badge badgeContent={11} color="secondary">
                <LocalMallOutlinedIcon />
            </Badge>
            </IconButton>
            <p>Bag</p>
        </MenuItem>
        <MenuItem onClick={handleProfileMenuOpen}>
            <IconButton
            aria-label="account of current user"
            aria-controls="primary-search-account-menu"
            aria-haspopup="true"
            color="inherit"
            >
            <AccountCircle />
            </IconButton>
            <p>Profile</p>
        </MenuItem>
        </Menu>
    );

    return (
        <div className={classes.grow}>
        <AppBar position="static">
            <Toolbar>
            <IconButton
                edge="start"
                className={classes.menuButton}
                color="inherit"
                aria-label="open drawer"
            >
                <MenuIcon />
            </IconButton>
            <Link to="/" className="">
                <img className="" src={logo} alt="Logo" height="50" />
            </Link>
            <div className={classes.grow} />
            <div className={classes.sectionDesktop}>
                <IconButton aria-label="show 17 new notifications" color="inherit">
                    <Badge badgeContent={17} color="secondary">
                        <NotificationsIcon />
                    </Badge>
                </IconButton>
                <IconButton aria-label="show 17 new notifications" color="inherit">
                    <Badge badgeContent={17} color="secondary">
                        <LocalMallOutlinedIcon />
                    </Badge>
                </IconButton>
                <IconButton color="inherit" onClick={handleSettingMenuOpen}
                    aria-controls={settingMenuId}
                    aria-haspopup="true"
                    className={classes.button}>
                    <SettingsIcon />
                </IconButton>
                <Button
                    color="inherit"
                    aria-label="account of current user"
                    aria-controls={menuId}
                    aria-haspopup="true"
                    className={classes.button}
                    onClick={handleProfileMenuOpen}
                    endIcon={<AccountCircle />}
                >
                    Profile
                </Button>
            </div>
            <div className={classes.sectionMobile}>
                <IconButton
                aria-label="show more"
                aria-controls={mobileMenuId}
                aria-haspopup="true"
                onClick={handleMobileMenuOpen}
                color="inherit"
                >
                <MoreIcon />
                </IconButton>
            </div>
            </Toolbar>
        </AppBar>
        {renderMobileMenu}
        {renderSettingMenu}
        {renderMenu}
        </div>
    );
}

export default Header;