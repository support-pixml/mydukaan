import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import logo from '../../images/logo-dark.png';
import { AppBar, Badge, Button, Divider, IconButton, List, ListItem, ListItemIcon, ListItemText, makeStyles, Menu, MenuItem, SwipeableDrawer, Toolbar } from '@material-ui/core';
import AccountCircle from '@material-ui/icons/AccountCircle';
import NotificationsIcon from '@material-ui/icons/Notifications';
import MoreIcon from '@material-ui/icons/MoreVert';
import LocalMallOutlinedIcon from '@material-ui/icons/LocalMallOutlined';
import SettingsIcon from '@material-ui/icons/Settings';
import AddCategory from '../../containers/Categories/addCategory';
import { useDispatch, useSelector } from 'react-redux';
import { isUserLoggedIn, signout } from '../../actions/auth';
import { AddBox, ListTwoTone } from '@material-ui/icons';

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
    modal: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    paper: {
        backgroundColor: theme.palette.background.paper,
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: theme.spacing(2, 4, 3),
    },
    list: {
        width: 250,
    },
    fullList: {
        width: 'auto',
    },
}));

const Header = () => {
    const classes = useStyles();
    const [anchorEl, setAnchorEl] = useState(null);
    const [mobileMoreAnchorEl, setMobileMoreAnchorEl] = useState(null);
    const [open, setOpen] = useState(false);
    const [categoryOpen, setCategoryOpen] = useState(false);
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const auth = useSelector(state => state.auth.authData);

    const cartItems = localStorage.getItem('cart') ? JSON.parse(localStorage.getItem('cart')) : [];
    console.log(cartItems);
    const dispatch = useDispatch();
    useEffect(() => {
        if(!auth?.user)
        {
            dispatch(isUserLoggedIn());
        }
    }, []);
    

    const isMenuOpen = Boolean(anchorEl);
    const isMobileMenuOpen = Boolean(mobileMoreAnchorEl);

    const handleProfileMenuOpen = (event) => {
        setAnchorEl(event.currentTarget);
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

    const handleOpen = () => {
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
    };

    const handleCategoryOpen = () => {
        setCategoryOpen(true);
    };

    const handleCategoryClose = () => {
        setCategoryOpen(false);
    };

    const handleSidebarOpen = () => {
        setSidebarOpen(true);
    };

    const handleSidebarClose = () => {
        setSidebarOpen(false);
    };

    const logout = () => {
        dispatch(signout());
    }

    const pathname = window.location.pathname;

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

    const renderAuthMenu = (
        <Menu
            anchorEl={anchorEl}
            anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
            id={menuId}
            keepMounted
            transformOrigin={{ vertical: 'top', horizontal: 'right' }}
            open={isMenuOpen}
            onClose={handleMenuClose}
            >
            <MenuItem><li className="" color="inherit"><span onClick={logout}>Sign Out</span></li></MenuItem>
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
            <IconButton color="inherit" onClick={handleSidebarOpen}
                aria-label="setting"
                aria-haspopup="true"
                className={classes.button}>
                <SettingsIcon />
            </IconButton>
            <p>Settings</p>
        </MenuItem>
        <MenuItem>
            <IconButton aria-label="show 11 new notifications" color="inherit">
            <Badge badgeContent={cartItems.length} color="secondary">
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

    const categoryModal = (
        <AddCategory handleClose={handleCategoryClose} open={categoryOpen} />
    )   

    const list = (
        <div
            className={classes.list}
            role="presentation"
            onClick={handleSidebarClose}
            onKeyDown={handleSidebarClose}
        >
            <List>
                <ListItem>
                    <Link to="/add-product" className="btn btn-block">
                        <AddBox /> Add Product
                    </Link>
                </ListItem>
                <ListItem>
                    <Link to="/add-product" className="btn btn-block">
                        <ListTwoTone /> Products
                    </Link>
                </ListItem>
                <ListItem>
                    <Button
                        aria-haspopup="true"
                        className="btn btn-block"
                        onClick={handleCategoryOpen}
                    >
                        <AddBox />&nbsp;&nbsp;Add Category
                    </Button>
                </ListItem>
                <ListItem>
                    <Link to="/show-categories" className="btn btn-block">
                        <ListTwoTone /> Categories
                    </Link>
                </ListItem>
            </List>
            <Divider />
            <List>
                <ListItem button>
                    <ListItemIcon></ListItemIcon>
                    <ListItemText primary="Orders" />
                </ListItem>
                <ListItem button>
                    <ListItemIcon></ListItemIcon>
                    <ListItemText primary="Users" />
                </ListItem>
            </List>
        </div>
    );

    const sidebar = ( 
        <SwipeableDrawer
            anchor="left"
            onClose={handleSidebarClose}
            onOpen={handleSidebarOpen}
            open={sidebarOpen}
        >
            {list}
        </SwipeableDrawer>
    );

    return (
        <div className={classes.grow}>
        <AppBar position="fixed">
            <Toolbar>
            <Link to="/" className="">
                <img className="" src={logo} alt="Logo" height="50" />
            </Link>
            <div className={classes.grow} />
            <div className={classes.sectionDesktop}>
                {pathname === '/signin' || pathname === '/signup' ? 
                null :  <>
                <IconButton aria-label="show new notifications" color="inherit">
                    <Badge badgeContent={cartItems.length} color="secondary">
                        <LocalMallOutlinedIcon />
                    </Badge>
                </IconButton>
                <IconButton color="inherit" onClick={handleSidebarOpen}
                    aria-label="setting"
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
                </Button></>
                }
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
        {auth?.user ? renderAuthMenu : renderMenu}
        {categoryModal}
        {sidebar}
        </div>
    );
}

export default Header;