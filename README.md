# 排隊叫號系統
有號碼牌的使用者可以被帶入桌

## 基本功能

- 分為管理員、使用者系統
- 使用者註冊功能
- 使用者登入功能
- 使用者個人資料更新功能
- 使用者登出功能
- 使用者需要登入才能使用服務

## 排隊叫號系統

後台（管理員）

 - Database 內建 5 張桌子
 - 列出所有桌子以及對應的狀態（可否使用）
 - 客人離開（本來佔用的桌子變為空閒狀態）
 - 顯示系統目前輪到的號碼
 - 重置排隊號碼（排隊號碼從 1 開始）
 - 叫號 & 帶客入桌
 - skip 號碼牌

前台（使用者）

- 領取號碼牌（若重複領取，則拋棄舊有的號碼，以新的替代）
- 顯示當前使用者所領取的號碼
- 顯示系統目前輪到的號碼

# wait-in-line
A user with a card can be leaded to a desk.

## Basic features

 - It comes with admin and user system.
 - User register function.
 - User login function.
 - User personal data update function.
 - User logout function.
 - Except for register function, other services are only accessible after logged in.

## wait in line system

For admin only
 - Database has built-in 5 desks.
 - List all desks' statuses (available).
 - Lead customers away from desks (Change desk's status from occupied to available).
 - Show current number card in system.
 - Reset number card.
 - Lead customers to desks.
 - Skip number card.

For user
 - Get a number card (If taking repeatedly abandon the previous one, and keep the latest one).
 - Show user's number card.
 - Show current number card in system.

## Getting Started

### step 1 - Clone project

`git clone git@github.com:ttnppedr/wait-in-line.git`

### step 2 - Change directory to wait-in-line

`cd wait-in-line`

### step 3 - Install packages

`composer install`

### step 4 - Create a database

### step 5 - Create .env file

`cp .env.example .env`

### step 6 - Set environment parameters

### step 7 - Generate a APP_KEY

`php artisan key:generate`

### step 8 - Migrate

`php artisan migrate --seed`

### step 9 - Hope you have a nice experience

