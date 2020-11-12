import DashboardUser from './components/dashboard/user/Dashboard.vue';
import BlogCategoryUser from './components/dashboard/user/BlogCategory.vue';
import BlogUser from './components/dashboard/user/Blog.vue';
import BlogListUser from './components/dashboard/user/BlogList.vue';
import Member from './components/dashboard/user/Member.vue';

export const myRoutes = [
    {
        path: '/user/dashboard',
        component: DashboardUser,
    },
    {
        path: '/user/blogCategory',
        component: BlogCategoryUser,
    },
    {
        path: '/user/blog',
        component: BlogUser,
    },
    {
        path: '/user/blogList',
        component: BlogListUser,
    },
    {
        path: '/user/member',
        component: Member,
    },
];