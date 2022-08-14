# Original task: Split name strings into their respective parts
import unittest
# Added extra new lines between definitions as per PEP8 standard


class NameSplit(unittest.TestCase):

    def test_split(self):
        # I would also add cases for empty string
        self.assertEqual(
            split(''),
            {
                'fName': '',
                'mNames': [],
                'lName': ''
            }
        ),
        # and string of spaces. At least, if this was used for processing user input
        self.assertEqual(
            split('   '),
            {
                'fName': '',
                'mNames': [],
                'lName': ''
            }
        ),
        self.assertEqual(
            split('Michael Daniel Jäger'),
            {
                'fName': 'Michael',
                'mNames': ['Daniel'],
                'lName': 'Jäger'
            }
        ),
        self.assertEqual(
            split('LINUS HARALD christer WAHLGREN'),
            {
                'fName': 'Linus',
                'mNames': ['Harald', 'Christer'],
                'lName': 'Wahlgren'
            }
        ),
        self.assertEqual(
            split('Pippilotta Viktualia Rullgardina Krusmynta Efraimsdotter LÅNGSTRUMP'),
            {
                'fName': 'Pippilotta',
                'mNames': ['Viktualia', 'Rullgardina', 'Krusmynta', 'Efraimsdotter'],
                # I would assume, that original value of LÅNGSTRUMP was a mistype.
                # Because I doubt there would be a logic, that would expect last names to have title case,
                # unless they have Swedish å. It is a possibility, but that would break standardization.
                # Or maybe there is some historic reasoning behind this, that I am unaware of.
                'lName': 'Långstrump'
            }
        ),
        self.assertEqual(
            split('Kalle Anka'),
            {
                'fName': 'Kalle',
                'mNames': [],
                'lName': 'Anka'
            }
        ),
        self.assertEqual(
            split('Ghandi'),
            {
                'fName': 'Ghandi',
                'mNames': [],
                'lName': ''
            }
        ),


# Implement split
# Added type hint, because I prefer strict typing
def split(raw: str):
    # Trim the value
    raw = raw.strip(' ')
    # We need to actually do the split into array first, and split by space
    # I prefer explicitly setting delimiter, instead of using default
    new_list = raw.split(' ')
    # Then we need to title case all the values
    for index, name in enumerate(new_list):
        new_list[index] = name.title()
    # In return, we take the 1st element for first name (obviously) and
    # -1 (last) for last name, if we do have 2 items (otherwise - empty string).
    # For middle names we get everything from 2nd to 2nd to last elements, but only if length is more than 2
    # Otherwise we return empty list
    # We could theoretically compare values of names for this, but I think relying on length is more straightforward
    return {
            'fName': new_list[0],
            'mNames': new_list[1:-1] if len(new_list) > 2 else [],
            'lName': new_list[-1] if len(new_list) > 1 else ''
            }


if __name__ == '__main__':
    unittest.main()
