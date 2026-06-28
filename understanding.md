# Project Understanding: NB Logistics

## Overview
This is a Laravel-based project for logistics and billing management. 

## Tech Stack
- Framework: Laravel
- Language: PHP
- Database: MySQL (or compatible, typical for Laravel apps)
- Package Manager: Composer, npm

## Architecture & Structure
- Typical Laravel directory structure (`app/`, `config/`, `database/`, `routes/`, `resources/`, `public/`).
- Logic is likely distributed across standard Laravel MVC components.

## Development Rules
- Enforce strict adherence to Laravel best practices.
- Use Artisan commands for generating code, caching, etc.

## Change Log
- **2026-06-28**: Initialized `understanding.md` to bootstrap the Antigravity IDE rules.
- **2026-06-28**: Fixed issue with automatic Monthly P&L Report download on the Truck view page. Removed the unconditional iframe rendering in `MonthlyReport` Livewire component that caused the immediate trigger of the PDF download endpoint.
