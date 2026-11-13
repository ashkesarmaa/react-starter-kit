# Project Conventions

- These conventions are deliberate project decisions. Where they disagree with generic advice in a skill (including `laravel-best-practices` and `testing-best-practices`), follow these conventions.

## Request Flow

- A feature flows Route → Controller → Form Request → Action → Model → Inertia page.
- Controllers only validate, call one Action, and respond. Business logic lives in Actions, which use Eloquent directly. Do not add repository or DTO layers.

## Models

- Primary keys are UUIDs: `HasUuids` on the model, `$table->uuid('id')->primary()` in the migration, and `foreignUuid()` for foreign keys.
- Models are unguarded globally by `nunomaduro/essentials`. Do not add `$fillable` or `$guarded`.
- Document every column with a `@property-read` tag in the class PHPDoc, and list every column in `casts()`, including plain strings.
- Hide attributes with the `#[Hidden([...])]` attribute, not a `$hidden` property.
- Every model has a factory with useful states and a unit test asserting its `toArray()` keys.

## Migrations

- Write only the `up()` method. Do not write `down()`.

## Actions

- Name Actions `{Verb}{Model}` after the operation, e.g. `CreateQuote`, `UpdateQuote`, `DeleteQuote`.
- Mark secrets such as passwords with `#[SensitiveParameter]`.

## Controllers

- Controllers are `final readonly` classes with no base `Controller` class.
- Use only resourceful method names: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`. For any other operation, create a controller for the sub-resource (e.g. `UserPasswordController`, `UserEmailVerificationNotificationController`) instead of adding a custom method.
- Inject dependencies per method: the Form Request, the Action, and `#[CurrentUser] User $user` for the authenticated user instead of `$request->user()`.
- Respond with `to_route()` or `back()`. Report success on authenticated pages with `Inertia::flash('toast', ['type' => 'success', 'message' => __('...')])`.

## Form Requests

- Create one Form Request per write operation, named `{Create|Update|Delete}{Model}Request`, with a `rules()` method using array syntax and an array-shape PHPDoc return type.

## Routes

- Declare every route explicitly with `Route::get()`, `Route::post()`, etc. in `routes/web.php`. Do not use `Route::resource()`.
- Place routes in the matching middleware group (`auth`, `auth` + `verified`, or `guest`) under a `// {Resource}...` comment.
- Name routes `{kebab-resource}.{method}`, e.g. `user-profile.edit`.

## Frontend

- Inertia page paths mirror the controller and method: `UserProfileController::edit()` renders `user-profile/edit`, which lives in `resources/js/pages/user-profile/edit.tsx`.
- Never hardcode URLs. Import Wayfinder functions from `@/actions/...` or `@/routes/...`, and build forms with `<Form {...UserProfileController.update.form()}>`.
- Set page layouts with `Page.layout = [AppLayout, { breadcrumbs }]`.
- Build UI from the shadcn components in `resources/js/components/ui`, and add `data-test` attributes to elements that tests target.

## Testing

- `composer test` requires 100% line coverage and 100% type coverage of `app/`. Every PHP change in `app/` needs tests that keep both at 100%.
- Test Actions as unit tests in `tests/Unit/Actions/{Action}Test.php`, resolving them with `resolve(Action::class)`.
- Test controllers as feature tests in `tests/Feature/Controllers/{Controller}Test.php`, using `fromRoute()`, `assertRedirectToRoute()`, `assertInertia()`, and `assertInertiaFlash()`.
- Test models, rules, middleware, and providers as unit tests under `tests/Unit/{Models,Rules,Middleware,Providers}/`. Browser tests live in `tests/Browser`.
- `tests/Pest.php` already applies `RefreshDatabase`, freezes time, and blocks stray HTTP requests and processes. Do not repeat that setup in test files.
- Start test descriptions with a verb, e.g. `it('may update a user')`, `it('requires email')`, `it('renders profile edit page')`.

## Quality Gates

- Before finishing a feature, run `composer test`. It runs Pint, Rector, PHPStan at max level, type coverage, the full test suite with coverage, and the frontend lint, format, and type checks.
- Run `composer lint` to fix formatting and Rector findings automatically.
