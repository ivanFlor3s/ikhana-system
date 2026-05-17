import { ComponentFixture, TestBed } from '@angular/core/testing';
import { Button } from './button';

describe('Button', () => {
  let component: Button;
  let fixture: ComponentFixture<Button>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Button],
    }).compileComponents();

    fixture = TestBed.createComponent(Button);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  describe('default values', () => {
    it('should have appearance default to primary', () => {
      expect(component.appearance).toBe('primary');
    });

    it('should have variant default to solid', () => {
      expect(component.variant).toBe('solid');
    });

    it('should have size default to md', () => {
      expect(component.size).toBe('md');
    });

    it('should have disabled default to false', () => {
      expect(component.disabled).toBeFalse();
    });

    it('should have type default to button', () => {
      expect(component.type).toBe('button');
    });

    it('should have isBlock default to false', () => {
      expect(component.isBlock).toBeFalse();
    });
  });

  describe('template rendering', () => {
    it('should render a native button element', () => {
      const btn = fixture.nativeElement.querySelector('button');
      expect(btn).toBeTruthy();
    });

    it('should project content via ng-content', () => {
      const text = 'Click me';
      fixture.nativeElement.querySelector('button').textContent = text;
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button');
      expect(btn.textContent.trim()).toBe(text);
    });
  });

  describe('@Input() type', () => {
    it('should set button type property to submit', () => {
      fixture.componentRef.setInput('type', 'submit');
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.type).toBe('submit');
    });

    it('should set button type property to reset', () => {
      fixture.componentRef.setInput('type', 'reset');
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.type).toBe('reset');
    });

    it('should set button type property to button by default', () => {
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.type).toBe('button');
    });
  });

  describe('@Input() disabled', () => {
    it('should set native disabled property when disabled is true', () => {
      fixture.componentRef.setInput('disabled', true);
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.disabled).toBeTrue();
    });

    it('should set aria-disabled attribute when disabled is true', () => {
      fixture.componentRef.setInput('disabled', true);
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.getAttribute('aria-disabled')).toBe('true');
    });

    it('should not be disabled when disabled is false', () => {
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.disabled).toBeFalse();
    });

    it('should have disabled cursor classes when disabled', () => {
      fixture.componentRef.setInput('disabled', true);
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.classList).toContain('disabled:opacity-60');
      expect(btn.classList).toContain('disabled:cursor-not-allowed');
    });
  });

  describe('@Input() isBlock', () => {
    it('should not have block w-full class when isBlock is false', () => {
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.classList).not.toContain('block');
      expect(btn.classList).not.toContain('w-full');
    });

    it('should have block w-full class when isBlock is true', () => {
      fixture.componentRef.setInput('isBlock', true);
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.classList).toContain('block');
      expect(btn.classList).toContain('w-full');
    });
  });

  describe('classes() method', () => {
    it('should return a string', () => {
      expect(typeof component.classes()).toBe('string');
    });

    it('should return non-empty string', () => {
      expect(component.classes().length).toBeGreaterThan(0);
    });

    describe('size', () => {
      it('sm', () => {
        component.size = 'sm';
        expect(component.classes()).toContain('text-sm');
        expect(component.classes()).toContain('px-3');
        expect(component.classes()).toContain('py-1.5');
        expect(component.classes()).toContain('rounded-md');
      });

      it('md', () => {
        component.size = 'md';
        expect(component.classes()).toContain('text-sm');
        expect(component.classes()).toContain('px-4');
        expect(component.classes()).toContain('py-2');
        expect(component.classes()).toContain('rounded-md');
      });

      it('lg', () => {
        component.size = 'lg';
        expect(component.classes()).toContain('text-base');
        expect(component.classes()).toContain('px-5');
        expect(component.classes()).toContain('py-3');
        expect(component.classes()).toContain('rounded-lg');
      });
    });

    describe('variant', () => {
      it('solid has shadow-sm', () => {
        component.variant = 'solid';
        expect(component.classes()).toContain('shadow-sm');
      });

      it('outlined has shadow-none', () => {
        component.variant = 'outlined';
        expect(component.classes()).toContain('shadow-none');
      });
    });

    describe('appearance', () => {
      it('primary solid', () => {
        component.appearance = 'primary';
        component.variant = 'solid';
        expect(component.classes()).toContain('bg-sky-600');
        expect(component.classes()).toContain('text-white');
        expect(component.classes()).toContain('hover:bg-sky-700');
      });

      it('primary outlined', () => {
        component.appearance = 'primary';
        component.variant = 'outlined';
        expect(component.classes()).toContain('text-sky-600');
        expect(component.classes()).toContain('border');
        expect(component.classes()).toContain('border-sky-600');
        expect(component.classes()).toContain('bg-transparent');
        expect(component.classes()).toContain('hover:bg-sky-300/10');
      });

      it('secondary solid', () => {
        component.appearance = 'secondary';
        component.variant = 'solid';
        expect(component.classes()).toContain('bg-neutral-200');
        expect(component.classes()).toContain('text-neutral-800');
        expect(component.classes()).toContain('hover:bg-neutral-300');
      });

      it('secondary outlined', () => {
        component.appearance = 'secondary';
        component.variant = 'outlined';
        expect(component.classes()).toContain('text-neutral-700');
        expect(component.classes()).toContain('border');
        expect(component.classes()).toContain('border-neutral-300');
        expect(component.classes()).toContain('bg-transparent');
        expect(component.classes()).toContain('hover:bg-neutral-700/10');
      });
    });
  });

  describe('onClick', () => {
    it('should set isBeingClicked to true on click', () => {
      component.isBeingClicked.set(false);
      const event = new Event('click');
      component.onClick(event);
      expect(component.isBeingClicked()).toBeTrue();
    });

    it('should reset isBeingClicked to false after 200ms', (done) => {
      component.isBeingClicked.set(false);
      const event = new Event('click');
      component.onClick(event);
      expect(component.isBeingClicked()).toBeTrue();

      setTimeout(() => {
        expect(component.isBeingClicked()).toBeFalse();
        done();
      }, 250);
    });

    it('should prevent click when disabled', () => {
      component.disabled = true;
      component.isBeingClicked.set(false);
      const event = new Event('click');
      spyOn(event, 'preventDefault');
      spyOn(event, 'stopImmediatePropagation');
      component.onClick(event);
      expect(event.preventDefault).toHaveBeenCalled();
      expect(event.stopImmediatePropagation).toHaveBeenCalled();
      expect(component.isBeingClicked()).toBeFalse();
    });
  });

  describe('under-pressure class', () => {
    it('should not have under-pressure class initially', () => {
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.classList).not.toContain('under-pressure');
    });

    it('should have under-pressure class when isBeingClicked is true', () => {
      component.isBeingClicked.set(true);
      fixture.detectChanges();
      const btn = fixture.nativeElement.querySelector('button') as HTMLButtonElement;
      expect(btn.classList).toContain('under-pressure');
    });
  });
});
