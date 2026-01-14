# Car Wash Cycle Spec (Admin Dashboard + System Alignment)

## هدف الملف
الملف ده هو "مصدر الحقيقة" (Source of Truth) لدورة العمل (Cycle) الخاصة بخدمة Car Wash داخل السيستم.
أي AI Agent أو Developer يقرأ الملف ده لازم:
1) يراجع إن المشروع الحالي ماشي بنفس الـFlow.
2) لو فيه اختلافات: يطلع **Checklist أولاً** بالتعديلات المطلوبة (قبل ما يكتب أي كود).
3) بعد الموافقة أو اعتماد الخطة يبدأ تنفيذ التعديلات.

---

## المصطلحات الأساسية
- **One-Time Order**: طلب مرة واحدة.
- **Package / Subscription**: اشتراك/باكدج (شهري مثلاً).
- **Car Wash Subscription**: اشتراك غسيل سيارات (دوري).
- **Dry Wash Subscription**: نوع اشتراك (غسيل/تنضيف دوري يتم بواسطة personal/cleaning team).
- **Admin**: الأدمن المسؤول عن مراجعة الطلب وتعيين الفريق.
- **Driver Team**: فريق السائقين/التوصيل/التنفيذ (حسب المشروع).
- **Cleaning Team**: فريق تنظيف (لـ Dry wash).
- **Schedule**: جدول دائم أو متكرر يتم إنشاؤه للاشتراكات.

---

## الـFlow الأساسي (As-Is Cycle)
### 1) User selects Car Wash Service
- المستخدم يختار خدمة Car Wash من التطبيق.

### 2) User chooses Order Type
المستخدم يختار نوع الطلب:
- (A) One-time
- (B) Package / Subscription

### 3A) One-time path
1. Fill Order Info (بيانات الطلب)
2. Send Order to Admin (إرسال للأدمن)
3. Admin reviews request (مراجعة)
4. Admin assigns Driver Team (تعيين فريق)
5. Team receives order (الفريق يستلم)
6. Team notifies start of service (بدء الخدمة)
7. Perform car wash (تنفيذ)
8. Team confirms completion (تأكيد اكتمال)
9. Notification sent to customer (إشعار للعميل)

### 3B) Package / Subscription path
1. Send Subscription to Admin
2. Admin reviews request
3. Admin chooses subscription type:
   - Car wash subscription
   - Dry wash subscription

#### 3B-1) Car Wash Subscription
1. Admin assigns Driver Team
2. Create Permanent Schedule
3. Schedules managed by admins
4. After each appointment: driver confirms it's done

#### 3B-2) Dry Wash Subscription
1. Admin assigns Cleaning Team
2. Create Permanent Schedule
3. Schedules managed by admins
4. After each appointment: driver/team confirms it's done

---

## ملاحظات من الـDiagram
- package: "more than normal wash per month" (عامل زي عروض/باقات)
- subscription: "dry wash type for the cars done by personal"
- Scheduling: يتم بواسطة Admin، وبعدين confirmations بعد كل appointment.

---

## متطلبات النظام (System Requirements)
### A) Entities / Tables المطلوبة (Conceptually)
1. bookings (طلبات)
2. services (الخدمات)
3. users/customers
4. driver_vehicles + drivers (أو أي Structure مكافئ)
5. booking_car_assignments (تعيينات عربيات/سواقين)
6. subscriptions (لو موجودة) أو package_orders
7. schedules / schedule_items (جدول دائم للـsubscriptions)

> لو المشروع بيستخدم payload_data بدل جداول subscription/schedule لازم توضيح ده تحت.

### B) Statuses المطلوبة (اقتراح واضح)
#### Booking statuses (One-time)
- pending_admin_review
- approved
- assigned
- in_progress
- completed
- cancelled

#### Subscription statuses
- pending_admin_review
- active
- paused
- cancelled
- expired (اختياري)

#### Appointment statuses (داخل جدول الاشتراك)
- scheduled
- started
- done
- missed (اختياري)
- cancelled

---

## نقاط لازم تتأكد منها داخل المشروع الحالي (Verification Checklist)
AI Agent لازم يدور ويطلع نتيجة لكل نقطة:

### 1) UI / UX
- هل فيه شاشة بتخلّي اليوزر يختار: One-time vs Subscription؟
- هل admin dashboard عنده مكان:
  - Reviewing requests
  - Approve/Reject
  - Assign team (Driver أو Cleaning)
  - Create schedule (permanent)
  - إدارة مواعيد الاشتراك
  - Confirmation بعد كل appointment

### 2) Backend Flow
- هل عندنا endpoints واضحة لـ:
  - Create one-time booking
  - Create subscription request
  - Admin review subscription
  - Admin assign team
  - Generate schedule items
  - Mark appointment as done

### 3) Data Model
- هل subscription موجود كـ table؟
- هل schedule موجود كـ table؟
- هل confirmation بعد كل appointment متسجل في DB؟
- هل فيه audit trail (assigned_by / timestamps)؟

### 4) Conflicts / vehicle availability
- هل النظام بيمنع تعارض المواعيد لنفس العربية/الفريق؟
- هل timeline view بتقرأ من assignments/schedules صح؟

---

## Expected Output من AI Agent (مهم جدًا)
قبل أي تعديل كود، الـAI Agent لازم يطلع:

### (1) Gap Report
- Where the current implementation differs from this spec.

### (2) Plan of changes (Ordered)
مثال:
1. Add missing statuses
2. Add subscription tables or normalize payload structure
3. Add schedule generation job/service
4. Update admin UI actions
5. Update timeline aggregation to include subscription appointments
6. Add confirmation endpoint + UI

### (3) Then Code Changes
- PR-style summary
- Files to create/modify
- Migration changes (لو موجود)
- Tests (لو موجود)

---

## Acceptance Criteria (Definition of Done)
السيستم يعتبر ماشي صح لما:
- One-time: يمشي end-to-end مع إشعارات و completion.
- Subscription: admin يقدر يعمل schedule دائم ويتابع confirmations.
- Timeline: يظهر One-time + Subscription appointments بشكل واضح.
- No overlap: تعارضات العربية/الفريق تتمنع أو تتعرض بوضوح.

---

## Appendix: Diagram Reference
This spec is based on the attached flow diagram (car wash cycle).
